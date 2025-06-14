<?php

namespace App\Filament\Resources\LabResource;

use App\Models\User;
use Filament\Forms\Components\Select;
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
