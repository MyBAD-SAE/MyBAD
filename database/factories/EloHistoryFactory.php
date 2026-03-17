<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EloHistoryFactory extends Factory
{
    public function definition(): array
    {
        $eloBefore = $this->faker->randomFloat(2, 800, 2400);
        
        return [
            'elo_before' => $eloBefore,
            'elo_after' => $eloBefore + $this->faker->randomFloat(2, -100, 100),
            'player_id' => $this->faker->uuid(),
        ];
    }
}
