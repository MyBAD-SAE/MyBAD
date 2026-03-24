<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Models\ClassParticipant;
use App\Models\EloHistory;
use App\Models\GameMatch;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ClassementController extends Controller
{
    public function index(): Response
    {
        $user    = Auth::guard('player')->user();
        $player  = $user->player;
        $classId = $player?->selectedParticipation()?->school_class_id;

        $classes = $player
            ? $player->classParticipants()->with('schoolClass')->get()
                ->map(fn ($cp) => ['id' => $cp->school_class_id, 'name' => $cp->schoolClass->name])
                ->values()->all()
            : [];

        $players = $this->getRankingForCurrentPlayer($classId);

        return Inertia::render('Player/Classements', [
            'players'         => $players,
            'classes'         => $classes,
            'selectedClassId' => $classId,
        ]);
    }

    public function getRankingForCurrentPlayer(?int $classId = null): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('player')->user();
        $player = $user->player;

        $participation = ClassParticipant::where('participantable_type', Player::class)
            ->where('participantable_id', $player->id)
            ->when($classId, fn ($q) => $q->where('school_class_id', $classId))
            ->first();

        if (! $participation) {
            return [];
        }

        $schoolClassId = $participation->school_class_id;

        $participants = ClassParticipant::where('school_class_id', $schoolClassId)
            ->where('participantable_type', Player::class)
            ->with('participantable.user')
            ->orderByDesc('elo_rating')
            ->get();

        $playerIds  = $participants->pluck('participantable_id');
        $matchStats = $this->getBulkMatchStats($playerIds, $schoolClassId);
        $eloTrends  = $this->getBulkEloTrends($participants);

        return $participants->values()->map(function ($participant, $index) use ($matchStats, $eloTrends) {
            $playerId = $participant->participantable_id;
            $playerUser = $participant->participantable->user;

            $wins = $matchStats[$playerId]['wins'] ?? 0;
            $losses = $matchStats[$playerId]['losses'] ?? 0;
            $total = $wins + $losses;
            $winRate = $total > 0 ? round(($wins / $total) * 100) : 0;

            return [
                'rank' => $index + 1,
                'name' => $playerUser->full_name,
                'avatar' => $playerUser->profile_picture,
                'elo' => (float) $participant->elo_rating,
                'wins' => $wins,
                'losses' => $losses,
                'trend' => $eloTrends[$playerId] ?? 0,
                'winRate' => $winRate,
            ];
        })->all();
    }

    private function getBulkMatchStats($playerIds, int $schoolClassId): array
    {
        $stats = $playerIds->mapWithKeys(fn ($id) => [$id => ['wins' => 0, 'losses' => 0]])->toArray();

        GameMatch::whereHas('classSession', fn ($q) => $q->where('school_class_id', $schoolClassId))
            ->with('players')
            ->get()
            ->each(function ($match) use (&$stats) {
                if ($match->players->count() !== 2) return;

                $a = $match->players->first();
                $b = $match->players->last();

                if ($a->pivot->score > $b->pivot->score) {
                    $stats[$a->id]['wins']++;
                    $stats[$b->id]['losses']++;
                } elseif ($b->pivot->score > $a->pivot->score) {
                    $stats[$b->id]['wins']++;
                    $stats[$a->id]['losses']++;
                }
            });

        return $stats;
    }

    private function getBulkEloTrends($participants): array
    {
        $participantToPlayer = $participants->pluck('participantable_id', 'id');

        return EloHistory::whereIn('participant_id', $participantToPlayer->keys())
            ->get()
            ->groupBy('participant_id')
            ->mapWithKeys(fn ($histories, $participantId) => [
                $participantToPlayer[$participantId] => round(
                    $histories->sum(fn ($h) => $h->elo_after - $h->elo_before),
                    1
                ),
            ])
            ->toArray();
    }
}
