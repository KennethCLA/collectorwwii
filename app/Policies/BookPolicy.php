<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;

class BookPolicy extends AdminOnlyPolicy
{
    public function viewAny(?User $user): bool
    {
        return $this->deny();
    }
    public function view(?User $user, Book $book): bool
    {
        return $this->deny();
    }
    public function create(?User $user): bool
    {
        return $this->deny();
    }
    public function update(?User $user, Book $book): bool
    {
        return $this->deny();
    }
    public function delete(?User $user, Book $book): bool
    {
        return $this->deny();
    }
}
