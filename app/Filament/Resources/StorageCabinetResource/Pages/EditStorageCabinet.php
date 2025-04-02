<?php

namespace App\Filament\Resources\StorageCabinetResource\Pages;

use App\Filament\Resources\StorageCabinetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStorageCabinet extends EditRecord
{
    protected static string $resource = StorageCabinetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
