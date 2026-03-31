<?php

namespace App\Services\Player;

use App\Models\ClassParticipant;

class EloDetailsService
{
    public function getData(?ClassParticipant $participation, ?int $classId): array
    {
        $currentElo = (float) ($participation?->elo_rating ?? 0);

        if (!$participation || !$classId) {
            return [
                'currentElo'   => $currentElo,
                'bestElo'      => $currentElo,
                'eloWeekDiff'  => 0,
                'eloHistory'   => [],
                'rank'         => null,
                'totalPlayers' => 0,
            ];
        }

        $histories   = $participation->eloHistories()->oldest()->get();
        $eloHistory  = [];
        $bestElo     = $currentElo;
        $eloWeekDiff = 0;

        if ($histories->isNotEmpty()) {
            $eloHistory = $histories->map(fn ($h) => [
                'elo'  => (float) $h->elo_after,
                'date' => $h->created_at->format('d/m'),
            ])->prepend([
                'elo'  => (float) $histories->first()->elo_before,
                'date' => $histories->first()->created_at->format('d/m'),
            ])->values()->all();

            $bestElo = max($currentElo, $histories->max('elo_after'), $histories->max('elo_before'));

            $recentHistories = $histories->filter(fn ($h) => $h->created_at->gte(now()->subDays(7)));
            if ($recentHistories->isNotEmpty()) {
                $eloWeekDiff = round($currentElo - (float) $recentHistories->first()->elo_before, 1);
            }
        }

        $totalPlayers = ClassParticipant::forClass($classId)->forPlayerType()->count();
        $rank         = ClassParticipant::forClass($classId)->forPlayerType()
            ->where('elo_rating', '>', $participation->elo_rating)
            ->count() + 1;

        return [
            'currentElo'   => $currentElo,
            'bestElo'      => round((float) $bestElo, 1),
            'eloWeekDiff'  => $eloWeekDiff,
            'eloHistory'   => $eloHistory,
            'rank'         => $rank,
            'totalPlayers' => $totalPlayers,
        ];
    }
}
