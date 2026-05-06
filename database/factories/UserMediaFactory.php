<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserMediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'media_id'     => Media::factory(),
            'status'       => fake()->randomElement(['planned', 'watching', 'completed']),
            'progress'     => fake()->numberBetween(0, 100),
            'started_at'   => fake()->optional()->dateTimeBetween('-6 months', 'now'),
            'completed_at' => fake()->optional()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
