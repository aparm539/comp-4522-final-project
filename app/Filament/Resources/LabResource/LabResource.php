<?php

namespace App\Filament\Resources\LabResource;

use App\Filament\Resources\LabResource\Pages;
use App\Filament\Resources\LabResource\RelationManagers\StorageLocationRelationManager;
use App\Models\Lab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LabResource extends Resource
{
    protected static ?string $model = Lab::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $activeNavigationIcon = 'heroicon-s-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(LabForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(LabTable::columns())
            ->actions(LabTable::actions())
            ->bulkActions(LabTable::bulkActions());

    }

    public static function getRelations(): array
    {
        return [
            StorageLocationRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLabs::route('/'),
            'edit' => Pages\EditLab::route('/{record}/edit'),
            'view' => Pages\ViewLab::route('/{record}'),
        ];
    }
}
