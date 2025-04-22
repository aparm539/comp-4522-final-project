<?php

namespace App\Filament\Resources\ChemicalResource;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class ChemicalForm
{
    public static function make(): array
    {
        return [
            TextInput::make('cas')->required(),
            TextInput::make('name')->required(),
            Toggle::make('ishazardous')
                ->default(true)
                ->required(),
        ];
    }

}
