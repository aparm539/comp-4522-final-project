<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Chemicals', 1000),
            Stat::make('Locations due for Reconciliation', 3),
            Stat::make('Misplaced Containers', 10)
                ->description('During most recent reconciliations')

            //
        ];
    }
}
