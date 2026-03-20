<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Controllers\player\ClassementController;
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
        $matchStats = ['wins' => 0, 'losses' => 0, 'total' => 0, 'sessions' => []];
        $totalMatches = 0;

        if ($player) {
            $matches = GameMatch::whereHas('players', fn ($q) => $q->where('player_id', $player->id))
                ->with(['players', 'classSession'])
                ->get();

            // Regrouper par session
            $bySession = [];
            foreach ($matches as $match) {
                $myScore = $match->players->firstWhere('id', $player->id)->pivot->score;
                $oppScore = $match->players->where('id', '!=', $player->id)->first()->pivot->score;
                $won = $myScore > $oppScore;

                $totalMatches++;

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

            // Stats des 4 dernières séances
            $last4Sessions = array_slice($bySession, -4);
            foreach ($last4Sessions as $s) {
                $matchStats['wins'] += $s['wins'];
                $matchStats['losses'] += $s['losses'];
                $matchStats['total'] += $s['wins'] + $s['losses'];
            }
            $matchStats['sessions'] = collect($last4Sessions)->map(fn ($s) => [
                'wins' => $s['wins'],
                'losses' => $s['losses'],
                'date' => $s['date'],
            ])->values()->all();

            // Elo sur les 4 dernières séances
            if ($participant) {
                $history = EloHistory::where('player_id', $player->id)
                    ->oldest()
                    ->get();

                if ($history->isNotEmpty()) {
                    // Prendre les entrées correspondant aux 4 dernières séances
                    $totalEntries = $history->count();
                    $recentCount = $matchStats['total'];
                    $recentHistory = $history->slice(max(0, $totalEntries - $recentCount));

                    $eloDiff = round((float) $participant->elo_rating - (float) $recentHistory->first()->elo_before, 2);
                    $eloHistory = $recentHistory->pluck('elo_after')->prepend($recentHistory->first()->elo_before)->values()->all();
                }
            }
        }


        $winStreak = 0;
        if ($player) {
            $sortedMatches = $matches->sortByDesc(fn ($m) => ($m->classSession?->date ?? '') . '_' . str_pad($m->id, 10, '0', STR_PAD_LEFT));
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

        $rankingPlayers = app(ClassementController::class)->getRankingForCurrentPlayer();

        return Inertia::render('Player/Dashboard', [
            'participant' => $participant ? ClassParticipantResource::make($participant)->resolve() : null,
            'eloDiff' => $eloDiff,
            'eloHistory' => $eloHistory,
            'matchStats' => $matchStats,
            'totalMatches' => $totalMatches,
            'winStreak' => $winStreak,
            'rankingPlayers' => $rankingPlayers,
        ]);
    }
}
