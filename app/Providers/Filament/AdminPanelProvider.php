<?php

namespace App\Providers\Filament;

use App\Filament\Resources\ChemicalResource\ChemicalResource;
use App\Filament\Resources\ContainerResource\ContainerResource;
use App\Filament\Resources\LocationResource\LocationResource;
use App\Filament\Resources\ReconciliationResource\ReconciliationResource;
use App\Filament\Resources\StorageCabinetResource\StorageCabinetResource;
use App\Filament\Resources\UserResource\UserResource;
use Exception;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->databaseNotifications()
            ->id('admin')
            ->path('')
            ->login()
            ->topNavigation()
            ->breadcrumbs(false)
            ->colors([
                'primary' => 'rgb(18, 50, 80)',
            ])
            ->resources([
                ChemicalResource::class,
                ContainerResource::class,
                LocationResource::class,
                ReconciliationResource::class,
                StorageCabinetResource::class,
                UserResource::class])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
