<?php

namespace App\Filament\Resources\ChemicalResource;

use App\Models\WhmisHazardClass;
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
            Select::make('whmis_hazard_class_id')
                ->label('WHMIS Hazard Class')
                ->options(WhmisHazardClass::all()->pluck('class_name', 'id'))
                ->searchable()
                ->required(),
        ];
    }
}
