<?php

namespace App\Filament\Resources\StorageLocationResource;

use App\Filament\Resources\StorageLocationResource\RelationManagers\ContainersRelationManager;
use App\Models\StorageLocation;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class StorageLocationResource extends Resource
{
    protected static ?string $model = StorageLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $activeNavigationIcon = 'heroicon-s-folder-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(StorageLocationForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(StorageLocationTable::columns())
            ->actions(StorageLocationTable::actions())
            ->bulkActions(StorageLocationTable::bulkActions())
            ->headerActions(StorageLocationTable::headerActions());
    }

    public static function getRelations(): array
    {
        return [
            ContainersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStorageLocations::route('/'),
            'create' => Pages\CreateStorageLocation::route('/create'),
            'edit' => Pages\EditStorageLocation::route('/{record}/edit'),
            'view' => Pages\ViewStorageLocation::route('/{record}'),
        ];
    }
}
