<?php

namespace App\Policies;

use App\Models\User;

class ContainerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        return true; // All authenticated users can view containers
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(): bool
    {
        return true; // All authenticated users can view individual containers
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isResearcher() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->isResearcher() || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->isAdmin() || $user->isResearcher(); // Only admins can delete containers
    }
}
