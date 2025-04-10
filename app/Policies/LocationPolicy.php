<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // All users can view locations
    }

    public function view(User $user, Location $location): bool
    {
        return true; // All users can view individual locations
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Location $location): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Location $location): bool
    {
        return $user->hasRole('admin');
    }
} 