<?php
namespace App\Policies;

use App\Models\Episode;
use App\Models\User;

class EpisodePolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Episode $episode): bool
    {
        return $episode->season->media->user_id === $user->id;
    }

    public function delete(User $user, Episode $episode): bool
    {
        return $episode->season->media->user_id === $user->id;
    }
}