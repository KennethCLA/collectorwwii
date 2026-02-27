<?php

namespace App\Policies;

use App\Models\Banknote;
use App\Models\User;

class BanknotePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Banknote $banknote): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role_id === 1;
    }

    public function update(User $user, Banknote $banknote): bool
    {
        return $user->role_id === 1;
    }

    public function delete(User $user, Banknote $banknote): bool
    {
        return $user->role_id === 1;
    }
}
