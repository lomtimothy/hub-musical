<?php

namespace App\Policies;

use App\Models\Studio;
use App\Models\StudioSession;
use App\Models\User;

class StudioSessionPolicy
{
    public function view(User $user, StudioSession $studioSession): bool
    {
        return $user->isAdmin()
            || $user->id === $studioSession->booked_by
            || $user->id === $studioSession->studio->owner_id
            || $studioSession->musicians()->whereKey($user->id)->exists();
    }

    public function create(User $user, Studio $studio): bool
    {
        return $studio->is_active
            && ($user->isMusician() || $user->isAdmin());
    }

    public function cancel(User $user, StudioSession $studioSession): bool
    {
        return $user->isAdmin()
            || $user->id === $studioSession->booked_by;
    }
}
