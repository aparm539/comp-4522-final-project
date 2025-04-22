<?php

namespace App\Filament\Resources\LocationResource;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class LocationForm
{
    public static function make(): array
    {
        return [
            TextInput::make('room_number')
                ->label('Room Number'),
            TextInput::make('description')
                ->label('Description'),
            Select::make('supervisor_id')
                ->label('Supervisor')
                ->options(User::all()->pluck('name', 'id'))
                ->searchable(),
        ];
    }

}
