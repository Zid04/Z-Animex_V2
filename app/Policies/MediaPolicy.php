<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;
use App\Models\UserMedia;

class MediaPolicy
{
    /*
    |--------------------------------
    | VIEW
    |--------------------------------
    */

    public function view(User $user, Media $media): bool
    {
        // Approuvé et public = visible par tous
        if ($media->approved && $media->is_public) {
            return true;
        }

        // Owner direct = toujours visible
        if ($media->user_id === $user->id) {
            return true;
        }

        // Accès via watchlist (partage privé)
        return UserMedia::where('user_id', $user->id)
            ->where('media_id', $media->id)
            ->exists();
    }

    /*
    |--------------------------------
    | UPDATE — seul le créateur
    |--------------------------------
    */

    public function update(User $user, Media $media): bool
    {
        return $media->user_id === $user->id;
    }

    /*
    |--------------------------------
    | DELETE — seul le créateur
    |--------------------------------
    */

    public function delete(User $user, Media $media): bool
    {
        return $media->user_id === $user->id;
    }

    /*
    |--------------------------------
    | CREATE
    |--------------------------------
    */

    public function create(User $user): bool
    {
        return true;
    }
}