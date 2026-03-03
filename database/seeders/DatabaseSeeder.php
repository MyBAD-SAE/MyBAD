<?php

namespace Database\Seeders;

use App\Models\GameMatch;
use App\Models\User;
use App\Services\EloService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $elo = new EloService();

        // Create test users with known passwords
        $users = collect();

        $users->push(User::factory()->create([
            'name' => 'Lucas Martin',
            'email' => 'lucas@mybad.test',
            'elo_rating' => 1000,
        ]));

        $users->push(User::factory()->create([
            'name' => 'Emma Dupont',
            'email' => 'emma@mybad.test',
            'elo_rating' => 1000,
        ]));

        $names = [
            'Thomas Bernard', 'Lea Petit', 'Hugo Moreau',
            'Chloe Laurent', 'Nathan Leroy', 'Manon Roux',
            'Enzo Garcia', 'Camille David', 'Louis Bertrand',
            'Jade Fournier', 'Arthur Girard',
        ];

        foreach ($names as $name) {
            $users->push(User::factory()->create([
                'name' => $name,
                'elo_rating' => 1000,
            ]));
        }

        // Simulate 30 completed matches
        for ($i = 0; $i < 30; $i++) {
            $pair = $users->random(2);
            $challenger = $pair->first();
            $challenged = $pair->last();

            // Generate random badminton scores
            $scores = $this->generateScores();

            $challengerSets = 0;
            $challengedSets = 0;

            foreach ($scores as $set) {
                if ($set['challenger'] > $set['challenged']) {
                    $challengerSets++;
                } else {
                    $challengedSets++;
                }
            }

            $winner = $challengerSets > $challengedSets ? $challenger : $challenged;
            $loser = $winner->id === $challenger->id ? $challenged : $challenger;

            $result = $elo->calculate($winner->elo_rating, $loser->elo_rating);

            $match = GameMatch::create([
                'challenger_id' => $challenger->id,
                'challenged_id' => $challenged->id,
                'winner_id' => $winner->id,
                'status' => 'completed',
                'challenger_score_set1' => $scores[0]['challenger'],
                'challenged_score_set1' => $scores[0]['challenged'],
                'challenger_score_set2' => $scores[1]['challenger'],
                'challenged_score_set2' => $scores[1]['challenged'],
                'challenger_score_set3' => $scores[2]['challenger'] ?? null,
                'challenged_score_set3' => $scores[2]['challenged'] ?? null,
                'played_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                'elo_change' => $result['change'],
            ]);

            $winner->update([
                'elo_rating' => $result['winner_new_rating'],
                'matches_played' => $winner->matches_played + 1,
                'matches_won' => $winner->matches_won + 1,
            ]);

            $loser->update([
                'elo_rating' => $result['loser_new_rating'],
                'matches_played' => $loser->matches_played + 1,
                'matches_lost' => $loser->matches_lost + 1,
            ]);

            // Refresh models to get updated ELO
            $winner->refresh();
            $loser->refresh();
        }

        // Add a pending challenge for demo
        GameMatch::create([
            'challenger_id' => $users[1]->id,
            'challenged_id' => $users[0]->id,
            'status' => 'pending',
        ]);

        // Add an accepted match for demo
        GameMatch::create([
            'challenger_id' => $users[0]->id,
            'challenged_id' => $users[2]->id,
            'status' => 'accepted',
        ]);
    }

    private function generateScores(): array
    {
        $sets = [];

        // Set 1
        $sets[] = $this->generateSetScore();

        // Set 2
        $sets[] = $this->generateSetScore();

        // Check if set 3 is needed
        $s1winner = $sets[0]['challenger'] > $sets[0]['challenged'] ? 'challenger' : 'challenged';
        $s2winner = $sets[1]['challenger'] > $sets[1]['challenged'] ? 'challenger' : 'challenged';

        if ($s1winner !== $s2winner) {
            // Need set 3
            $sets[] = $this->generateSetScore();
        }

        return $sets;
    }

    private function generateSetScore(): array
    {
        $winScore = 21;
        $loserScore = rand(5, 19);

        // Sometimes close scores
        if (rand(1, 4) === 1) {
            $loserScore = rand(19, 28);
            $winScore = $loserScore + 2;
            if ($winScore > 30) {
                $winScore = 30;
                $loserScore = 29;
            }
        }

        // Randomly assign winner
        if (rand(0, 1)) {
            return ['challenger' => $winScore, 'challenged' => $loserScore];
        }
        return ['challenger' => $loserScore, 'challenged' => $winScore];
    }
}
