<?php

namespace App\Filament\Resources\LocationResource;

use App\Filament\Resources\LocationResource\RelationManagers\StorageCabinetRelationManager;
use App\Models\Location;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $activeNavigationIcon = 'heroicon-s-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(LocationForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(LocationTable::columns())
            ->actions(LocationTable::actions())
            ->bulkActions(LocationTable::bulkActions());

    }

    public static function getRelations(): array
    {
        return [
            StorageCabinetRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocations::route('/'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
            'view' => Pages\ViewLocation::route('/{record}'),
        ];
    }
}
