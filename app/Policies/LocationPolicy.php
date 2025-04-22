<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;

    public function viewAny(): bool
    {
        return true; // All users can view locations
    }

    public function view(): bool
    {
        return true; // All users can view individual locations
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
