<?php

namespace App\Services\Match;

use App\Models\AlgorithmParameter;
use App\Models\ClassParticipant;
use App\Models\EloHistory;
use App\Models\Player;

class EloService
{
    /**
     * Calcule le changement d'ELO basé sur l'écart de rang entre les joueurs.
     *
     * Formule : winner_points (depuis algorithm_parameters) + écart_rang / 10
     * Plafonné entre 0 et 10. Le perdant reçoit l'inverse.
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

        $rankings = ClassParticipant::forClass($schoolClassId)
            ->forPlayerType()
            ->orderByDesc('elo_rating')
            ->pluck('participantable_id')
            ->values();

        $myRank = $rankings->search($playerId);
        $opponentRank = $rankings->search($opponentId);

        if ($myRank === false || $opponentRank === false) {
            return 0;
        }

        $myRank += 1;
        $opponentRank += 1;

        $isWinner = $myScore > $opponentScore;

        $winnerRank = $isWinner ? $myRank : $opponentRank;
        $loserRank = $isWinner ? $opponentRank : $myRank;
        $rankDiff = $winnerRank - $loserRank;

        $basePoints = $this->getBasePoints($schoolClassId, $rankDiff);

        $winnerChange = max(0, min(10, $basePoints + ($rankDiff / 10)));

        return $isWinner ? $winnerChange : -$winnerChange;
    }

    /**
     * Met à jour l'ELO d'un joueur et enregistre l'historique.
     */
    public function updateElo(string $playerId, float $eloChange, int $schoolClassId): void
    {
        $participation = ClassParticipant::forClass($schoolClassId)
            ->forPlayer($playerId)
            ->first();

        if (!$participation) {
            return;
        }

        $eloBefore = (float) $participation->elo_rating;
        $eloAfter = $eloBefore + $eloChange;

        $participation->update(['elo_rating' => $eloAfter]);

        $participation->eloHistories()->create([
            'elo_before' => $eloBefore,
            'elo_after'  => $eloAfter,
        ]);
    }

    /**
     * Récupère les points de base depuis les paramètres de l'algorithme.
     */
    private function getBasePoints(int $schoolClassId, int $rankDiff): float
    {
        $param = AlgorithmParameter::where('school_class_id', $schoolClassId)
            ->where('min_diff', '<=', $rankDiff)
            ->where('max_diff', '>=', $rankDiff)
            ->first();

        if (!$param) {
            $param = AlgorithmParameter::where('school_class_id', $schoolClassId)
                ->orderByRaw('ABS(min_diff + max_diff - ?) ASC', [$rankDiff * 2])
                ->first();
        }

        return $param?->winner_points ?? 0.5;
    }
}