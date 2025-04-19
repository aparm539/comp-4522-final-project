<?php

namespace App\Providers;

use App\Models\Chemical;
use App\Models\Container;
use App\Models\Location;
use App\Models\Reconciliation;
use App\Models\StorageCabinet;
use App\Models\User;
use App\Policies\ChemicalPolicy;
use App\Policies\ContainerPolicy;
use App\Policies\LocationPolicy;
use App\Policies\ReconciliationPolicy;
use App\Policies\StorageCabinetPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Chemical::class => ChemicalPolicy::class,
        Container::class => ContainerPolicy::class,
        Location::class => LocationPolicy::class,
        StorageCabinet::class => StorageCabinetPolicy::class,
        Reconciliation::class => ReconciliationPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
