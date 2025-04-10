<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view the user list
    }

    public function view(User $user, User $model): bool
    {
        return true; // All authenticated users can view individual users
    }

    public function create(User $user): bool
    {
        return $user->isAdmin(); // Only admins can create new users
    }

    public function update(User $user, User $model): bool
    {
        return $user->isAdmin(); // Only admins can update users
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin(); // Only admins can delete users
    }
} 