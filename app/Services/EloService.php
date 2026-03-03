<?php

namespace App\Services;

class EloService
{
    private const K_FACTOR = 32;
    private const MIN_RATING = 100;

    public function calculate(int $winnerRating, int $loserRating): array
    {
        $expectedWinner = 1 / (1 + pow(10, ($loserRating - $winnerRating) / 400));
        $expectedLoser = 1 / (1 + pow(10, ($winnerRating - $loserRating) / 400));

        $change = (int) round(self::K_FACTOR * (1 - $expectedWinner));

        $newWinnerRating = max(self::MIN_RATING, $winnerRating + $change);
        $newLoserRating = max(self::MIN_RATING, $loserRating - $change);

        return [
            'winner_new_rating' => $newWinnerRating,
            'loser_new_rating' => $newLoserRating,
            'change' => $change,
        ];
    }
}
