<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClassSessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'is_active' => $this->faker->boolean(),
            'class_id' => ClassFactory::new(),
        ];
    }
}
