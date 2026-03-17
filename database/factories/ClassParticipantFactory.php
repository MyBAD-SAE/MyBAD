<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClassParticipantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'participantable_type' => $this->faker->randomElement(['App\Models\Player', 'App\Models\AdminUser']),
            'participantable_id' => $this->faker->uuid(),
            'elo_rating' => $this->faker->randomFloat(2, 800, 2400),
            'class_id' => ClassFactory::new(),
        ];
    }
}
