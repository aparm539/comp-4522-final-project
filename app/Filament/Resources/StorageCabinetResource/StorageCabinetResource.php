<?php

namespace App\Filament\Resources\StorageCabinetResource;

use App\Filament\Resources\StorageCabinetResource\RelationManagers\ContainersRelationManager;
use App\Models\StorageCabinet;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class StorageCabinetResource extends Resource
{
    protected static ?string $model = StorageCabinet::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $activeNavigationIcon = 'heroicon-s-folder-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(StorageCabinetForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(StorageCabinetTable::columns())
            ->actions(StorageCabinetTable::actions())
            ->bulkActions(StorageCabinetTable::bulkActions());
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
            'index' => Pages\ListStorageCabinets::route('/'),
            'create' => Pages\CreateStorageCabinet::route('/create'),
            'edit' => Pages\EditStorageCabinet::route('/{record}/edit'),
            'view' => Pages\ViewStorageCabinet::route('/{record}'),
        ];
    }
}
