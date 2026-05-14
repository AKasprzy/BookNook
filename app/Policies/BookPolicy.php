<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Book $book): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Book $book): bool
    {
        return $user->hasAnyRole([
            Role::Admin->value,
            Role::SuperAdmin->value,
        ]);
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->hasAnyRole([
            Role::Admin->value,
            Role::SuperAdmin->value,
        ]);
    }

    public function forceDelete(User $user, Book $book): bool
    {
        return $user->hasAnyRole([
            Role::Admin->value,
            Role::SuperAdmin->value,
        ]);
    }
}
