<?php

namespace App\Services\Dashboard;

use App\Models\GameMatch;
use App\Models\ClassParticipant;
use App\Models\Player;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Récupère les stats de matchs groupées par séance (4 dernières séances).
     */
    public function getMatchStats(Player $player, ?int $classId): array
    {
        $matches = $this->getPlayerMatches($player, $classId);

        $bySession = [];
        $totalMatches = 0;

        foreach ($matches as $match) {
            $myScore = $match->players->firstWhere('id', $player->id)->pivot->score;
            $oppScore = $match->players->where('id', '!=', $player->id)->first()->pivot->score;
            $won = $myScore > $oppScore;
            $totalMatches++;

            $sessionId = $match->class_session_id;
            if (!isset($bySession[$sessionId])) {
                $date = $match->classSession?->date;
                $bySession[$sessionId] = [
                    'wins'     => 0,
                    'losses'   => 0,
                    'raw_date' => $date,
                    'date'     => $date ? Carbon::parse($date)->format('d/m') : null,
                ];
            }

            $won ? $bySession[$sessionId]['wins']++ : $bySession[$sessionId]['losses']++;
        }

        usort($bySession, fn ($a, $b) => ($a['raw_date'] ?? '') <=> ($b['raw_date'] ?? ''));

        $last4Sessions = array_slice($bySession, -4);

        $stats = ['wins' => 0, 'losses' => 0, 'total' => 0, 'sessions' => []];
        foreach ($last4Sessions as $s) {
            $stats['wins'] += $s['wins'];
            $stats['losses'] += $s['losses'];
            $stats['total'] += $s['wins'] + $s['losses'];
        }
        $stats['sessions'] = collect($last4Sessions)->map(fn ($s) => [
            'wins'   => $s['wins'],
            'losses' => $s['losses'],
            'date'   => $s['date'],
        ])->values()->all();

        return [
            'matchStats'   => $stats,
            'totalMatches' => $totalMatches,
            'matches'      => $matches,
        ];
    }

    /**
     * Calcule l'historique ELO et la différence sur les 4 dernières séances.
     */
    public function getEloData(ClassParticipant $participant, int $recentMatchCount): array
    {
        $history = $participant->eloHistories()->oldest()->get();

        if ($history->isEmpty() || $recentMatchCount === 0) {
            return ['eloDiff' => 0, 'eloHistory' => []];
        }

        $recentHistory = $history->slice(max(0, $history->count() - $recentMatchCount));

        if ($recentHistory->isEmpty()) {
            return ['eloDiff' => 0, 'eloHistory' => []];
        }

        return [
            'eloDiff'    => round((float) $participant->elo_rating - (float) $recentHistory->first()->elo_before, 2),
            'eloHistory' => $recentHistory->pluck('elo_after')
                ->prepend($recentHistory->first()->elo_before)
                ->values()
                ->all(),
        ];
    }

    /**
     * Calcule la série de victoires consécutives en cours.
     */
    public function getWinStreak(Player $player, $matches): int
    {
        $sorted = $matches->sortByDesc(
            fn (GameMatch $m) => ($m->classSession?->date ?? '') . '_' . str_pad($m->id, 10, '0', STR_PAD_LEFT)
        );

        $streak = 0;
        foreach ($sorted as $match) {
            $myScore = $match->players->firstWhere('id', $player->id)->pivot->score;
            $oppScore = $match->players->where('id', '!=', $player->id)->first()->pivot->score;

            if ($myScore > $oppScore) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Récupère la liste des classes du joueur.
     */
    public function getPlayerClasses(Player $player): array
    {
        return $player->classParticipants()
            ->with('schoolClass')
            ->get()
            ->map(fn (ClassParticipant $cp) => [
                'id'   => $cp->school_class_id,
                'name' => $cp->schoolClass->name,
            ])
            ->values()
            ->all();
    }

    /**
     * Récupère tous les matchs d'un joueur pour une classe donnée.
     */
    private function getPlayerMatches(Player $player, ?int $classId)
    {
        return GameMatch::forPlayer($player->id)
            ->when($classId, fn ($q) => $q->forClass($classId))
            ->with(['players', 'classSession'])
            ->get();
    }
}
