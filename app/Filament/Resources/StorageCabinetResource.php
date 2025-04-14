<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StorageCabinetResource\Pages;
use App\Filament\Resources\StorageCabinetResource\RelationManagers;
use App\Filament\Resources\StorageCabinetResource\RelationManagers\ContainersRelationManager;
use App\Models\StorageCabinet;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use App\Models\Location;
use App\Models\User;
class StorageCabinetResource extends Resource
{
    protected static ?string $model = StorageCabinet::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $activeNavigationIcon = 'heroicon-s-folder-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('location_id')

                    ->label('Location')
                    ->options(Location::all()->pluck('room_number', 'id'))
                    ->required()
                    ->searchable(),
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location.user.name')
                ->Label('Supervisor'),
                Tables\Columns\TextColumn::make('location.room_number'),
                Tables\Columns\TextColumn::make('name')
                ->Label('Storage Cabinet'),
                //
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
            ContainersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStorageCabinets::route('/'),
            'create' => Pages\CreateStorageCabinet::route('/create'),
            'edit' => Pages\EditStorageCabinet::route('/{record}/edit'),
            'view' => Pages\ViewStorageCabinet::route('/{record}')
        ];
    }
}
