<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassParticipantResource;
use App\Http\Resources\PlayerResource;
use App\Models\EloHistory;
use App\Models\GameMatch;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth('player')->user();
        $player = $user->player;

        $participant = $player
            ?->classParticipants()
            ->with('participantable.user')
            ->first();

        $eloDiff = 0;
        $eloHistory = [];
        if ($player && $participant) {
            $history = EloHistory::where('player_id', $player->id)
                ->oldest()
                ->get();

            if ($history->isNotEmpty()) {
                $eloDiff = round((float) $participant->elo_rating - (float) $history->first()->elo_before, 2);
                $eloHistory = $history->pluck('elo_after')->prepend($history->first()->elo_before)->values()->all();
            }
        }

        $matchStats = ['wins' => 0, 'losses' => 0, 'total' => 0, 'sessions' => []];

        if ($player) {
            $matches = GameMatch::whereHas('players', fn ($q) => $q->where('player_id', $player->id))
                ->with(['players', 'classSession'])
                ->get();

            $bySession = [];
            foreach ($matches as $match) {
                $myScore = $match->players->firstWhere('id', $player->id)->pivot->score;
                $oppScore = $match->players->where('id', '!=', $player->id)->first()->pivot->score;
                $won = $myScore > $oppScore;

                $won ? $matchStats['wins']++ : $matchStats['losses']++;
                $matchStats['total']++;

                $sessionId = $match->class_session_id;
                if (!isset($bySession[$sessionId])) {
                    $date = $match->classSession?->date;
                    $bySession[$sessionId] = [
                        'wins' => 0,
                        'losses' => 0,
                        'raw_date' => $date,
                        'date' => $date ? \Carbon\Carbon::parse($date)->format('d/m') : null,
                    ];
                }
                $won ? $bySession[$sessionId]['wins']++ : $bySession[$sessionId]['losses']++;
            }

            usort($bySession, fn ($a, $b) => ($a['raw_date'] ?? '') <=> ($b['raw_date'] ?? ''));
            $matchStats['sessions'] = collect(array_slice($bySession, -5))->map(fn ($s) => [
                'wins' => $s['wins'],
                'losses' => $s['losses'],
                'date' => $s['date'],
            ])->values()->all();
        }


        $winStreak = 0;
        if ($player) {
            $sortedMatches = $matches->sortByDesc(fn ($m) => $m->classSession?->date ?? '');
            foreach ($sortedMatches as $match) {
                $myScore = $match->players->firstWhere('id', $player->id)->pivot->score;
                $oppScore = $match->players->where('id', '!=', $player->id)->first()->pivot->score;
                if ($myScore > $oppScore) {
                    $winStreak++;
                } else {
                    break;
                }
            }
        }

        return Inertia::render('Player/Dashboard', [
            'participant' => $participant ? ClassParticipantResource::make($participant)->resolve() : null,
            'eloDiff' => $eloDiff,
            'eloHistory' => $eloHistory,
            'matchStats' => $matchStats,
            'winStreak' => $winStreak,
        ]);
    }
}
