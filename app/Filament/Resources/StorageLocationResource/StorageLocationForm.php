<?php

namespace App\Filament\Resources\StorageLocationResource;

use App\Filament\Resources\LabResource\RelationManagers\StorageLocationRelationManager;
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
                ->maxLength(255),
            Select::make('lab_id')
                ->label('Lab')
                ->options(Lab::all()->pluck('room_number', 'id'))
                ->required()
                ->searchable()
                ->hiddenOn(StorageLocationRelationManager::class),
        ];
    }
}
