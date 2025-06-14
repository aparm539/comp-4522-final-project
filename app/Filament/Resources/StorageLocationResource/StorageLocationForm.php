<?php

namespace App\Filament\Resources\StorageLocationResource;

use App\Models\Lab;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class StorageLocationForm
{
    public static function make(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('barcode')
                ->required()
                ->maxLength(255),
            Select::make('lab_id')
                ->label('Lab')
                ->options(Lab::all()->pluck('room_number', 'id'))
                ->required()
                ->searchable(),
        ];
    }
}
