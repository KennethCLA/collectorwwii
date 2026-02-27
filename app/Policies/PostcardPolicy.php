<?php

namespace App\Policies;

use App\Models\Postcard;
use App\Models\User;

class PostcardPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Postcard $postcard): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role_id === 1;
    }

    public function update(User $user, Postcard $postcard): bool
    {
        return $user->role_id === 1;
    }

    public function delete(User $user, Postcard $postcard): bool
    {
        return $user->role_id === 1;
    }
}
