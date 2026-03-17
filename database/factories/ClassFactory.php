<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_year' => $this->faker->year(),
            'name' => $this->faker->words(2, true),
            'address' => $this->faker->address(),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
