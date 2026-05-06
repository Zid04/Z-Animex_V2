<?php
namespace App\Policies;

use App\Models\User;
use App\Models\UserRating;

class UserRatingPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, UserRating $rating): bool
    {
        return $rating->user_id === $user->id;
    }

    public function delete(User $user, UserRating $rating): bool
    {
        return $rating->user_id === $user->id;
    }
}