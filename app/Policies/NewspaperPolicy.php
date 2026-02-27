<?php

namespace App\Policies;

use App\Models\Newspaper;
use App\Models\User;

class NewspaperPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Newspaper $newspaper): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role_id === 1;
    }

    public function update(User $user, Newspaper $newspaper): bool
    {
        return $user->role_id === 1;
    }

    public function delete(User $user, Newspaper $newspaper): bool
    {
        return $user->role_id === 1;
    }
}
