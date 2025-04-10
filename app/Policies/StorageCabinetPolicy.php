<?php

namespace App\Policies;

use App\Models\StorageCabinet;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorageCabinetPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // All users can view storage cabinets
    }

    public function view(User $user, StorageCabinet $storageCabinet): bool
    {
        return true; // All users can view individual storage cabinets
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, StorageCabinet $storageCabinet): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, StorageCabinet $storageCabinet): bool
    {
        return $user->hasRole('admin');
    }
} 