<?php

namespace App\Policies;

use App\Models\User;

abstract class AdminOnlyPolicy
{
    /**
     * "Before" wordt altijd eerst uitgevoerd.
     * Als dit true teruggeeft, is alles toegelaten zonder verdere checks.
     */
    public function before(?User $user, string $ability): bool|null
    {
        // Niet ingelogd? laat andere checks beslissen (meestal false via middleware/guards)
        if (!$user) {
            return null;
        }

        // Pas aan naar jouw user-veld / methode:
        // bv. $user->is_admin, $user->isAdmin(), $user->role === 'admin', ...
        return $user->isAdmin() ? true : null;
    }

    /**
     * Default deny voor alle abilities die je niet expliciet definieert.
     */
    protected function deny(): bool
    {
        return false;
    }
}
