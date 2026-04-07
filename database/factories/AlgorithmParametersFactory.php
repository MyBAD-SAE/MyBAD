<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AlgorithmParametersFactory extends Factory
{
    public function definition(): array
    {
        return [
            'min_diff' => $this->faker->numberBetween(1, 50),
            'max_diff' => $this->faker->numberBetween(100, 500),
            'winner_points' => $this->faker->randomFloat(1, -10, 20),
            'class_id' => ClassFactory::new(),
        ];
    }
}
