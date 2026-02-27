<?php

namespace App\Policies;

use App\Models\MapLocation;
use App\Models\User;

class MapLocationPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, MapLocation $mapLocation): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role_id === 1;
    }

    public function update(User $user, MapLocation $mapLocation): bool
    {
        return $user->role_id === 1;
    }

    public function delete(User $user, MapLocation $mapLocation): bool
    {
        return $user->role_id === 1;
    }
}
