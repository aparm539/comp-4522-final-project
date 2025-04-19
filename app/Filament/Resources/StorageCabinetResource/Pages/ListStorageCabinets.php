<?php

namespace App\Filament\Resources\StorageCabinetResource\Pages;

use App\Filament\Resources\StorageCabinetResource\StorageCabinetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStorageCabinets extends ListRecords
{
    protected static string $resource = StorageCabinetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
