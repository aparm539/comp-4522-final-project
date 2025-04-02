<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContainerResource\Pages;
use App\Filament\Resources\ContainerResource\RelationManagers;
use App\Models\Chemical;
use App\Models\Container;
use App\Models\Location;
use App\Models\StorageCabinet;
use App\Models\UnitOfMeasure;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContainerResource extends Resource
{
    protected static ?string $model = Container::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $activeNavigationIcon = 'heroicon-s-rectangle-stack';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('chemical_id')
                    ->label('CAS #')
                    ->options(Chemical::all()->pluck('cas', 'id'))
                    ->searchable()
                ->required(),
                Select::make('unit_of_measure_id')
                ->label('Unit of Measure')
                ->options(UnitOfMeasure::all()->pluck('abbreviation', 'id'))
                ->searchable()
                ->required(),

                TextInput::make('quantity')
                ->label('Quantity')
                    ->numeric()
                    ->required(),
                Select::make('location_id')
                ->label('Location')
                ->options(Location::all()->pluck('room_number', 'id'))
                ->searchable()
                ->required(),
                Select::make('shelf_id')
                    ->label('Shelf')
                    ->options(function($get){
                        $location = $get('location_id');
                        return StorageCabinet::all()->where('location_id', $location)->pluck('name', 'id');
                    })
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barcode'),
                TextColumn::make('chemical.cas')
                    ->label("CAS #"),
                TextColumn::make('chemical.name')
                    ->limit(25)
                    ->tooltip(fn ($record): string => ($record->chemical->name)),
                TextColumn::make('unitofmeasure.abbreviation')
                    ->formatStateUsing(fn ($state, $record) => ($record->quantity.' '.$record->unitofmeasure->abbreviation))
                    ->label('Quantity'),
                TextColumn::make('storageCabinet')
                    ->formatStateUsing(fn ($state, $record) => ($record->storageCabinet->location->room_number.' '.$record->storageCabinet->name))
                    ->label('Location'),
                TextColumn::make('chemical.ishazardous')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => match ($state) {
                        true => 'danger',
                        false => '-',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'danger',
                        '-' => 'primary',
                        default => 'primary'
                    })
                 ->label('Hazardous'),
                TextColumn::make('storageCabinet.location.user.name')
                ->label('Supervisor')

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContainers::route('/'),
        ];
    }
}
