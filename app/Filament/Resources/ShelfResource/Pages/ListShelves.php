<?php

namespace App\Filament\Resources\ShelfResource\Pages;

use App\Filament\Resources\StorageCabinetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShelves extends ListRecords
{
    protected static string $resource = StorageCabinetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
