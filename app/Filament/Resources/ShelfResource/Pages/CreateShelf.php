<?php

namespace App\Filament\Resources\ShelfResource\Pages;

use App\Filament\Resources\StorageCabinetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateShelf extends CreateRecord
{
    protected static string $resource = StorageCabinetResource::class;
}
