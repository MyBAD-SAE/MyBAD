<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameMatch>
 */
class GameMatchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'challenger_id' => User::factory(),
            'challenged_id' => User::factory(),
            'status' => 'completed',
        ];
    }
}
