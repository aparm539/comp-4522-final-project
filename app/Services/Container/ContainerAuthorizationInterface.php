<?php

namespace App\Services\Container;

use App\Models\User;

interface ContainerAuthorizationInterface
{
    public function canExport(User $user): bool;
    public function canImport(User $user): bool;
    public function canChangeLocation(User $user): bool;
} 