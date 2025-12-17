<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Item;

class ItemPolicy extends AdminOnlyPolicy
{
    public function viewAny(?User $user): bool
    {
        return $this->deny();
    }
    public function view(?User $user, Item $item): bool
    {
        return $this->deny();
    }
    public function create(?User $user): bool
    {
        return $this->deny();
    }
    public function update(?User $user, Item $item): bool
    {
        return $this->deny();
    }
    public function delete(?User $user, Item $item): bool
    {
        return $this->deny();
    }
}
