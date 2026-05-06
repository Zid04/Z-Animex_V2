<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Media;

class ProgressPolicy
{
    /**
     * voir progression d’un média
     */
    public function view(User $user, Media $media): bool
    {
        return app(MediaPolicy::class)->view($user, $media);
    }

    /**
     * marquer épisode vu
     */
    public function create(User $user, Media $media): bool
    {
        return app(MediaPolicy::class)->view($user, $media);
    }

    /**
     * supprimer progression
     */
    public function delete(User $user, Media $media): bool
    {
        return app(MediaPolicy::class)->view($user, $media);
    }
}