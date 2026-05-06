<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserMedia;

class UserMediaPolicy
{
    public function view(User $user, UserMedia $userMedia): bool
    {
        return $user->id === $userMedia->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, UserMedia $userMedia): bool
    {
        return $user->id === $userMedia->user_id;
    }

    public function delete(User $user, UserMedia $userMedia): bool
    {
        return $user->id === $userMedia->user_id;
    }
}