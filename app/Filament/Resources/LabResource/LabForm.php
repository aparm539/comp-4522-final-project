<?php

namespace App\Filament\Resources\LabResource;

use Filament\Forms\Components\TextInput;

class LabForm
{
    public static function make(): array
    {
        return [
            TextInput::make('room_number')
                ->label('Room Number'),
            TextInput::make('description')
                ->label('Description'),

        ];
    }
}
