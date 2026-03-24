<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Models\ClassParticipant;
use App\Models\EloHistory;
use App\Models\MatchPlayer;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RankingController extends Controller
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
        $entries = MatchPlayer::whereIn('player_id', $playerIds)
            ->whereHas('gameMatch.classSession', fn ($q) => $q->where('school_class_id', $schoolClassId))
            ->get(['game_match_id', 'player_id', 'score']);

        $stats = $playerIds->mapWithKeys(fn ($id) => [$id => ['wins' => 0, 'losses' => 0]])->toArray();

        $entries->groupBy('game_match_id')->each(function ($matchEntries) use (&$stats) {
            if ($matchEntries->count() !== 2) return;

            $a = $matchEntries->first();
            $b = $matchEntries->last();

            if ($a->score > $b->score) {
                $stats[$a->player_id]['wins']++;
                $stats[$b->player_id]['losses']++;
            } elseif ($b->score > $a->score) {
                $stats[$b->player_id]['wins']++;
                $stats[$a->player_id]['losses']++;
            }
        });

        return $stats;
    }

    private function getBulkEloTrends($participants): array
    {
        $playerIds = $participants->pluck('participantable_id');

        return EloHistory::whereIn('player_id', $playerIds)
            ->groupBy('player_id')
            ->selectRaw('player_id, ROUND(SUM(elo_after - elo_before), 1) as trend')
            ->pluck('trend', 'player_id')
            ->map(fn ($v) => (float) $v)
            ->toArray();
    }
}