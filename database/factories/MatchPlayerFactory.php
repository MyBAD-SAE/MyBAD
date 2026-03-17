<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MatchPlayerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'match_id' => MatchFactory::new(),
            'player_id' => $this->faker->uuid(),
            'score' => $this->faker->numberBetween(0, 100),
            'validated' => $this->faker->boolean(),
        ];
    }
}
