<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ItemPolicy
{
    private function isAdmin(User $user): bool
    {
        return (int) $user->role_id === 1;
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, Item $item): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Item $item): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Item $item): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Item $item): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Item $item): bool
    {
        return $this->isAdmin($user);
    }
}
