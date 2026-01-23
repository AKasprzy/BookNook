<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\BookEdition;
use App\Models\User;

class BookEditionPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, BookEdition $bookEdition): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, BookEdition $bookEdition): bool
    {
        return $user->hasAnyRole([
            Role::Admin->value,
            Role::SuperAdmin->value,
        ]);
    }

    public function delete(User $user, BookEdition $bookEdition): bool
    {
        return $user->hasAnyRole([
            Role::Admin->value,
            Role::SuperAdmin->value,
        ]);
    }
}
