<?php

namespace App\Policies;

use App\Models\Space;
use App\Models\User;

class SpacePolicy
{
    /**
     * Determine whether the user owns the space and may manage it.
     */
    public function manage(User $user, Space $space): bool
    {
        return $space->user_id === $user->id;
    }
}
