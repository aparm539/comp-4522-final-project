<?php

namespace App\Filament\Resources\ChemicalResource;

use App\Models\Chemical;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ChemicalResource extends Resource
{
    protected static ?string $model = Chemical::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $activeNavigationIcon = 'heroicon-s-beaker';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(ChemicalForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(ChemicalTable::columns())
            ->actions(ChemicalTable::actions())
            ->bulkActions(ChemicalTable::bulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChemicals::route('/'),
        ];
    }
}
