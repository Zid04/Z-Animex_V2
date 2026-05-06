<?php
namespace App\Policies;

use App\Models\User;
use App\Models\UserFavorite;
use App\Models\Media;

class UserFavoritePolicy
{
    public function create(User $user, Media $media): bool
    {
        return true;
    }

    public function delete(User $user, UserFavorite $favorite): bool
    {
        return $favorite->user_id === $user->id;
    }
}