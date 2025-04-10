<?php

namespace App\Policies;

use App\Models\Chemical;
use App\Models\User;

class ChemicalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view chemicals
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Chemical $chemical): bool
    {
        return true; // All authenticated users can view individual chemicals
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
    public function update(User $user, Chemical $chemical): bool
    {
        return $user->isResearcher() || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Chemical $chemical): bool
    {
        return $user->isAdmin(); // Only admins can delete chemicals
    }
} 