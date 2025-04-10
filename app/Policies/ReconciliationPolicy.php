<?php

namespace App\Policies;

use App\Models\Reconciliation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReconciliationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Reconciliation $reconciliation): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Reconciliation $reconciliation): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Reconciliation $reconciliation): bool
    {
        return $user->hasRole('admin');
    }
} 