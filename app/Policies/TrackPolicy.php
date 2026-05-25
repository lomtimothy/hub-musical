<?php

namespace App\Policies;

use App\Models\StudioSession;
use App\Models\Track;
use App\Models\User;

class TrackPolicy
{
    public function create(User $user, StudioSession $studioSession): bool
    {
        return $user->isAdmin()
            || $user->id === $studioSession->booked_by
            || $studioSession->musicians()->whereKey($user->id)->exists();
    }

    public function view(User $user, Track $track): bool
    {
        $studioSession = $track->studioSession;

        return $user->isAdmin()
            || $user->id === $track->uploaded_by
            || $user->id === $studioSession->booked_by
            || $user->id === $studioSession->studio->owner_id
            || $studioSession->musicians()->whereKey($user->id)->exists();
    }

    public function delete(User $user, Track $track): bool
    {
        return $user->isAdmin()
            || $user->id === $track->uploaded_by;
    }
}
