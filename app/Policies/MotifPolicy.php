<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Motif;
use App\Models\User;

class MotifPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Motif $motif): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            Role::Admin->value,
            Role::SuperAdmin->value,
        ]);
    }

    public function update(User $user, Motif $motif): bool
    {
        return $user->hasAnyRole([
            Role::Admin->value,
            Role::SuperAdmin->value,
        ]);
    }

    public function delete(User $user, Motif $motif): bool
    {
        return $user->hasAnyRole([
            Role::Admin->value,
            Role::SuperAdmin->value,
        ]);
    }
}
