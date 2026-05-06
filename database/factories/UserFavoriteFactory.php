<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFavoriteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'  => User::factory(),
            'media_id' => Media::factory(),
        ];
    }
}