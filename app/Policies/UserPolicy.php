<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Shelve;
use App\Models\User;

class UserPolicy
{
    public function destroy(User $user, Shelve $shelve): bool
    {
        return
            $user->id === $shelve->user_id ||
            $user->hasAnyRole([
                Role::Admin->value,
                Role::SuperAdmin->value,
            ]);
    }
}
