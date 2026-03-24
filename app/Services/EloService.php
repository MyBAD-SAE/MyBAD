<?php

namespace App\Services;

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
    public function calculateEloChange(string $playerId, string $opponentId, int $myScore, int $opponentScore): float
    {
        if ($myScore === $opponentScore) {
            return 0;
        }

        $participation = ClassParticipant::where('participantable_type', Player::class)
            ->where('participantable_id', $playerId)
            ->first();

        if (!$participation) {
            return 0;
        }

        $schoolClassId = $participation->school_class_id;

        // Classement de tous les joueurs de la classe par ELO décroissant
        $rankings = ClassParticipant::where('school_class_id', $schoolClassId)
            ->where('participantable_type', Player::class)
            ->orderByDesc('elo_rating')
            ->pluck('participantable_id')
            ->values();

        $myRank = $rankings->search($playerId);
        $opponentRank = $rankings->search($opponentId);

        if ($myRank === false || $opponentRank === false) {
            return 0;
        }

        // Rang commence à 1
        $myRank += 1;
        $opponentRank += 1;

        $isWinner = $myScore > $opponentScore;

        // Écart de rang du point de vue du vainqueur : positif = le perdant était mieux classé
        $winnerRank = $isWinner ? $myRank : $opponentRank;
        $loserRank = $isWinner ? $opponentRank : $myRank;
        $rankDiff = $loserRank - $winnerRank;

        $basePoints = $this->getBasePoints($schoolClassId, $rankDiff);
        $winnerChange = $basePoints + ($rankDiff / 10);

        // Plafonner entre 0 et 10
        $winnerChange = max(0, min(10, $winnerChange));

        // Du point de vue du joueur courant
        return $isWinner ? $winnerChange : -$winnerChange;
    }

    /**
     * Met à jour l'ELO d'un joueur et enregistre l'historique.
     */
    public function updateElo(string $playerId, float $eloChange): void
    {
        $participation = ClassParticipant::where('participantable_type', Player::class)
            ->where('participantable_id', $playerId)
            ->first();

        if (!$participation) {
            return;
        }

        $eloBefore = (float) $participation->elo_rating;
        $eloAfter = $eloBefore + $eloChange;

        $participation->update(['elo_rating' => $eloAfter]);

        EloHistory::create([
            'player_id' => $playerId,
            'elo_before' => $eloBefore,
            'elo_after' => $eloAfter,
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

        return $param ? $param->winner_points : 0.5;
    }
}