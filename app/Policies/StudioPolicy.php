<?php

namespace App\Policies;

use App\Models\Studio;
use App\Models\User;

class StudioPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Studio $studio): bool
    {
        if ($studio->is_active) {
            return true;
        }

        if (! $user) {
            return false;
        }

        return $user->isAdmin() || $user->id === $studio->owner_id;
    }

    public function create(User $user): bool
    {
        return $user->isProducer() || $user->isAdmin();
    }

    public function update(User $user, Studio $studio): bool
    {
        return $user->isAdmin() || $user->id === $studio->owner_id;
    }

    public function delete(User $user, Studio $studio): bool
    {
        return $user->isAdmin() || $user->id === $studio->owner_id;
    }

    public function restore(User $user, Studio $studio): bool
    {
        return $user->isAdmin() || $user->id === $studio->owner_id;
    }

    public function forceDelete(User $user, Studio $studio): bool
    {
        return false;
    }
}
