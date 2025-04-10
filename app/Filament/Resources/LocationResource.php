<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers\StorageCabinetRelationManager;
use App\Models\Location;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;
    protected static ?string $navigationIcon = 'gmdi-meeting-room-o';
    protected static ?string $activeNavigationIcon = 'gmdi-meeting-room-r';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('barcode')
                    ->label('Barcode')
                    ->disabled()
                    ->visible(fn ($record) => $record !== null) // Show only if the record exists (e.g., on edit)
                    ->dehydrated(false),
                TextInput::make('room_number')
                    ->disabled()
                    ->visible(fn ($record) => $record !== null) // Show only if the record exists (e.g., on edit)
                    ->dehydrated(false),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('room_number'),
                TextColumn::make('barcode'),
                TextColumn::make('description')
                    ->limit(20),
                TextColumn::make('user.name')
                    ->label('Supervisor')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')),
                ]),
            ]);
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
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }

}
