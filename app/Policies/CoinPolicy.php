<?php

namespace App\Policies;

use App\Models\Coin;
use App\Models\User;

class CoinPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Coin $coin): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role_id === 1;
    }

    public function update(User $user, Coin $coin): bool
    {
        return $user->role_id === 1;
    }

    public function delete(User $user, Coin $coin): bool
    {
        return $user->role_id === 1;
    }
}
