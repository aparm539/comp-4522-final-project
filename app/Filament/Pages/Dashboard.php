<?php

namespace App\Filament\Pages;

use App\Filament\Components\RoleSwitcher;
use Filament\Pages\Page;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            RoleSwitcher::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 3;
    }
}

