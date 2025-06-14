<?php

namespace App\Policies;

use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorageLocationPolicy
{
    use HandlesAuthorization;

    public function viewAny(): bool
    {
        return true; // All users can view storage locations
    }

    public function view(User $user): bool
    {
        return true; // All users can view individual storage locations
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
