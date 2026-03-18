<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PublicViewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'access_token' => Str::random(32),
            'class_id' => ClassFactory::new(),
        ];
    }
}
