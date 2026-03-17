<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MatchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'class_session_id' => ClassSessionFactory::new(),
        ];
    }
}
