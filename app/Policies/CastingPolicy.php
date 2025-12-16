<?php

namespace App\Policies;

use App\Models\Casting;
use App\Models\User;

class CastingPolicy
{
    public function update(User $user, Casting $casting): bool
    {
        return $user->id === $casting->user_id;
    }

    public function delete(User $user, Casting $casting): bool
    {
        return $user->id === $casting->user_id;
    }
}


