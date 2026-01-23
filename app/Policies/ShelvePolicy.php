<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Shelve;
use App\Models\User;

class ShelvePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Shelve $shelve): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Shelve $shelve): bool
    {
        return
            $user->id === $shelve->user_id ||
            $user->hasAnyRole([
                Role::Admin->value,
                Role::SuperAdmin->value,
            ]);
    }

    public function delete(User $user, Shelve $shelve): bool
    {
        return
            $user->id === $shelve->user_id ||
            $user->hasAnyRole([
                Role::Admin->value,
                Role::SuperAdmin->value,
            ]);
    }
}
