<?php

namespace App\Filament\Resources\ChemicalResource;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class ChemicalForm
{
    public static function make(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('cas')
                        ->label('CAS #')
                        ->required(),
                    TextInput::make('name')
                        ->required(),
                ]),
            \App\Livewire\HazardClassSelect::make('whmisHazardClasses')
                ->label('WHMIS Hazard Classes')
                ->relationship('whmisHazardClasses', 'id')
                ->required()
                ->columnSpanFull(),
        ];
    }
}
