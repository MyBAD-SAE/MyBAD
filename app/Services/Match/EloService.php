<?php

namespace App\Services\Match;

use App\Models\AlgorithmParameter;
use App\Models\ClassParticipant;
use App\Models\EloHistory;
use App\Models\Player;

class EloService
{
    /**
     * Calcule le changement d'ELO basé sur l'écart de score entre les joueurs
     */
    public function calculateEloChange(
        string $playerId,
        string $opponentId,
        int $myScore,
        int $opponentScore,
        int $schoolClassId,
    ): float {
        if ($myScore === $opponentScore) {
            return 0;
        }

        $isWinner = $myScore > $opponentScore;

        $winnerScore = $isWinner ? $myScore : $opponentScore;
        $loserScore = $isWinner ? $opponentScore : $myScore;
        $scoreDiff = $winnerScore - $loserScore;

        if ($isWinner) {
            $basePoints = $this->getBasePoints($schoolClassId, $scoreDiff);
            return round($basePoints + $scoreDiff, 1);
        }

        $basePoints = $this->getBasePoints($schoolClassId, -$scoreDiff);
        return round($basePoints - $scoreDiff, 1);
    }

    /**
     * Met à jour l'ELO d'un joueur et enregistre l'historique.
     */
    public function updateElo(string $playerId, float $eloChange, int $schoolClassId, int $gameMatchId): void
    {
        $participation = ClassParticipant::forClass($schoolClassId)
            ->forPlayer($playerId)
            ->first();

        if (!$participation) {
            return;
        }

        $eloBefore = (float) $participation->elo_rating;
        $eloAfter = max(0, round($eloBefore + $eloChange, 1));

        $participation->update(['elo_rating' => $eloAfter]);

        $participation->eloHistories()->create([
            'game_match_id' => $gameMatchId,
            'elo_before'    => $eloBefore,
            'elo_after'     => $eloAfter,
        ]);
    }

    /**
     * Récupère les points de base depuis les paramètres de l'algorithme.
     */
    private function getBasePoints(int $schoolClassId, int $scoreDiff): float
    {
        $param = AlgorithmParameter::where('school_class_id', $schoolClassId)
            ->where('min_diff', '<=', $scoreDiff)
            ->where('max_diff', '>=', $scoreDiff)
            ->first();

        if (!$param) {
            $param = AlgorithmParameter::where('school_class_id', $schoolClassId)
                ->orderByRaw('ABS(min_diff + max_diff - ?) ASC', [$scoreDiff * 2])
                ->first();
        }

        return $param?->winner_points ?? 5;
    }
}
