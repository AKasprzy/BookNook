<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Genre;
use App\Models\User;

class GenrePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Genre $genre): bool
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

    public function update(User $user, Genre $genre): bool
    {
        return $user->hasAnyRole([
            Role::Admin->value,
            Role::SuperAdmin->value,
        ]);
    }

    public function delete(User $user, Genre $genre): bool
    {
        return $user->hasAnyRole([
            Role::Admin->value,
            Role::SuperAdmin->value,
        ]);
    }
}
