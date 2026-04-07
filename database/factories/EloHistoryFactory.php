<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EloHistoryFactory extends Factory
{
    public function definition(): array
    {
        $eloBefore = $this->faker->randomFloat(1, 8000, 24000);

        return [
            'elo_before' => $eloBefore,
            'elo_after' => $eloBefore + $this->faker->randomFloat(1, -1000, 1000),
            'player_id' => $this->faker->uuid(),
        ];
    }
}
