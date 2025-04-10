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
use Illuminate\Database\Eloquent\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Filament\Exports\ContainerExporter;
use App\Filament\Imports\ContainerImporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;

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
                ->options(function() {
                    return Location::all()->mapWithKeys(function($location) {
                        return [$location->id => $location->room_number];
                    });
                })
                ->disableOptionWhen(function($value) {
                    $location = Location::find($value);
                    return $location && $location->hasOngoingReconciliation();
                })
                ->helperText(function() {
                    $ongoingLocations = Location::whereHas('reconciliations', function($query) {
                        $query->where('status', 'ongoing');
                    })->get();
                    
                    if ($ongoingLocations->isNotEmpty()) {
                        $locations = $ongoingLocations->pluck('room_number')->join(', ');
                        return "The following locations are currently being reconciled and cannot be selected: {$locations}";
                    }
                    return null;
                })
                ->searchable()
                ->dehydrated(false)
                ->required(),
                Select::make('storage_cabinet_id')
                    ->label('Storage Cabinet')
                    ->options(function($get){
                        $location = $get('location_id');
                        return StorageCabinet::all()->pluck('name', 'id');
                    })
                ->required(),
                TextInput::make('barcode')
                    ->label('Barcode')
                    ->disabled()
                    ->visible(fn ($record) => $record !== null) // Show only if the record exists (e.g., on edit)
                    ->dehydrated(false),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barcode')
                    ->sortable()
                    ->searchable()
                    ->width('100px'),
                TextColumn::make('chemical.cas')
                    ->label("CAS #")
                    ->sortable()
                    ->searchable()
                    ->width('120px'),
                TextColumn::make('chemical.name')
                    ->limit(20)
                    ->tooltip(fn ($record): string => ($record->chemical->name))
                    ->sortable()
                    ->searchable()
                    ->width('150px'),
                TextColumn::make('quantity')
                    ->sortable()
                    ->searchable()
                    ->width('80px'),
                TextColumn::make('unitofmeasure.abbreviation')
                    ->label('Unit')
                    ->sortable()
                    ->searchable()
                    ->width('60px'),
                TextColumn::make('storageCabinet.location.room_number')
                    ->label('Location')
                    ->formatStateUsing(fn ($state, $record) => $state . ' - ' . $record->storageCabinet->name)
                    ->sortable()
                    ->searchable()
                    ->width('180px'),
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
                    ->label('Hazardous')
                    ->sortable()
                    ->searchable()
                    ->width('100px'),
                TextColumn::make('storageCabinet.location.user.name')
                    ->label('Supervisor')
                    ->sortable()
                    ->searchable()
                    ->width('120px')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('chemical')
                    ->relationship('chemical', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('location')
                    ->relationship('storageCabinet.location', 'room_number')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('storage_cabinet')
                    ->relationship('storageCabinet', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('unit_of_measure')
                    ->relationship('unitOfMeasure', 'abbreviation')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('hazardous')
                    ->options([
                        '1' => 'Hazardous',
                        '0' => 'Non-Hazardous',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === '1') {
                            $query->whereHas('chemical', function ($q) {
                                $q->where('ishazardous', true);
                            });
                        } elseif ($data['value'] === '0') {
                            $query->whereHas('chemical', function ($q) {
                                $q->where('ishazardous', false);
                            });
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->slideOver(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ContainerExporter::class)
                    ->visible(fn () => auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')),
                ImportAction::make()
                    ->importer(ContainerImporter::class)
                    ->visible(fn () => auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')),
                Tables\Actions\Action::make('print')
                    ->label('Print All Containers')
                    ->icon('heroicon-o-printer')
                    ->action(function () {
                        $containers = Container::all();
                        return response()->streamDownload(function () use ($containers) {
                            $pdf = PDF::loadView('containers.print', [
                                'containers' => $containers
                            ]);
                            echo $pdf->stream();
                        }, 'containers.pdf');
                    })
            ])
            ->bulkActions([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')),
                    Tables\Actions\BulkAction::make('changeLocation')
                        ->label('Change Location')
                        ->icon('heroicon-o-map')
                        ->visible(fn () => auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                        ->form([
                            Select::make('location_id')
                                ->label('Location')
                                ->options(function() {
                                    return Location::all()->mapWithKeys(function($location) {
                                        return [$location->id => $location->room_number];
                                    });
                                })
                                ->disableOptionWhen(function($value) {
                                    $location = Location::find($value);
                                    return $location && $location->hasOngoingReconciliation();
                                })
                                ->helperText(function() {
                                    $ongoingLocations = Location::whereHas('reconciliations', function($query) {
                                        $query->where('status', 'ongoing');
                                    })->get();
                                    
                                    if ($ongoingLocations->isNotEmpty()) {
                                        $locations = $ongoingLocations->pluck('room_number')->join(', ');
                                        return "The following locations are currently being reconciled and cannot be selected: {$locations}";
                                    }
                                    return null;
                                })
                                ->searchable()
                                ->required(),
                            Select::make('storage_cabinet_id')
                                ->label('Storage Cabinet')
                                ->options(function($get){
                                    $location = $get('location_id');
                                    return StorageCabinet::where('location_id', $location)
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'location_id' => $data['location_id'],
                                    'storage_cabinet_id' => $data['storage_cabinet_id'],
                                ]);
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Change Location')
                        ->modalDescription('Are you sure you want to change the location and storage cabinet of the selected containers?')
                        ->modalSubmitActionLabel('Yes, change location'),
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
