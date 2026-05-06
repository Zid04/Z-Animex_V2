<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Episode;
use Illuminate\Database\Eloquent\Factories\Factory;

class EpisodeUserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'         => User::factory(),
            'episode_id'      => Episode::factory(),
            'watched_at'      => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'progress_seconds'=> fake()->numberBetween(0, 1800), // 0 à 30 min
        ];
    }
}
