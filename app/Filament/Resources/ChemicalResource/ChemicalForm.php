<?php

namespace App\Filament\Resources\ChemicalResource;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class ChemicalForm
{
    public static function make(): array
    {
        return [
            TextInput::make('cas')
                ->label('CAS #')
                ->required(),
            TextInput::make('name')->required(),
            \App\Forms\Components\HazardClassSelect::make('whmisHazardClasses')
                ->relationship('whmisHazardClasses', 'id')
                ->required(),
        ];
    }
}
