<?php

namespace App\Filament\Resources\StorageCabinetResource;

use App\Models\Location;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class StorageCabinetForm
{
    public static function make(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Select::make('location_id')
                ->label('Location')
                ->options(Location::all()->pluck('room_number', 'id'))
                ->required()
                ->searchable(),
        ];
    }
}
