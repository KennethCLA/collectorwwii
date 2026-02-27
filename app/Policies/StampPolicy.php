<?php

namespace App\Policies;

use App\Models\Stamp;
use App\Models\User;

class StampPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Stamp $stamp): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role_id === 1;
    }

    public function update(User $user, Stamp $stamp): bool
    {
        return $user->role_id === 1;
    }

    public function delete(User $user, Stamp $stamp): bool
    {
        return $user->role_id === 1;
    }
}
