<?php
// app/Policies/ItemPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Item;

class ItemPolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // publiek
    }

    public function view(?User $user, Item $item): bool
    {
        return true; // publiek
    }

    public function create(User $user): bool
    {
        return $user->role_id === 1;
    }

    public function update(User $user, Item $item): bool
    {
        return $user->role_id === 1;
    }

    public function delete(User $user, Item $item): bool
    {
        return $user->role_id === 1;
    }
}
