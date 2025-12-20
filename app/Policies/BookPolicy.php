<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;

class BookPolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // publiek
    }

    public function view(?User $user, Book $book): bool
    {
        return true; // publiek
    }

    public function create(User $user): bool
    {
        return $user->role_id === 1;
    }

    public function update(User $user, Book $book): bool
    {
        return $user->role_id === 1;
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->role_id === 1;
    }
}
