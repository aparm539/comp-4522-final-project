<?php

namespace App\Filament\Resources\StorageCabinetResource\RelationManagers;

use App\Models\Chemical;
use App\Models\Location;
use App\Models\Reconciliation;
use App\Models\StorageCabinet;
use App\Models\UnitOfMeasure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContainersRelationManager extends RelationManager
{
    protected static string $relationship = 'containers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('chemical_id')
                    ->label('Chemical')
                    ->options(Chemical::query()
                        ->with('whmisHazardClass')
                        ->get()
                        ->mapWithKeys(function (Chemical $chemical) {
                            return [
                                $chemical->id => "{$chemical->cas} - {$chemical->name} ({$chemical->whmisHazardClass->class_name})",
                            ];
                        }))
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
                    ->dehydrated(false)
                    ->options(function () {

                        return Location::all()->pluck('room_number', 'id');
                    })

                    ->disableOptionWhen(function ($value) {
                        $location = Location::find($value);

                        return $location && $location->hasOngoingReconciliation();
                    })
                    ->helperText(function () {
                        $ongoingLocations = Location::whereHas('reconciliations', function ($query) {
                            $query->where('status', 'ongoing');
                        })->get();

                        if ($ongoingLocations->isNotEmpty()) {
                            $locations = $ongoingLocations->pluck('room_number')->join(', ');

                            return "The following locations are currently being reconciled and cannot be selected: $locations";
                        }

                        return null;
                    })
                    ->searchable()
                    ->required(),
                Select::make('storage_cabinet_id')
                    ->label('Storage Cabinet')
                    ->options(function ($get) {
                        $location = $get('location_id');
                        if ($location) {

                            return StorageCabinet::where('location_id', $location)->pluck('name', 'id');
                        }

                        return StorageCabinet::all()->pluck('name', 'id');

                    })
                    ->required()
                    ->searchable(),
                TextInput::make('barcode')
                    ->label('Barcode')
                    ->required(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('barcode'),
                TextColumn::make('chemical.cas')
                    ->label('CAS #'),
                TextColumn::make('chemical.name')
                    ->limit(25)
                    ->tooltip(fn ($record): string => ($record->chemical->name)),
                TextColumn::make('unitofmeasure.abbreviation')
                    ->formatStateUsing(fn ($state, $record) => ($record->quantity.' '.$record->unitofmeasure->abbreviation))
                    ->label('Quantity'),
                TextColumn::make('storageCabinet')
                    ->formatStateUsing(fn ($state, $record) => ($record->storageCabinet->location->room_number.' '.$record->storageCabinet->name))
                    ->label('Location'),
                TextColumn::make('chemical.whmisHazardClass.class_name')
                    ->label('Hazard Class')
                    ->badge()
                    ->color(fn ($record): string => match ($record->chemical->whmisHazardClass->symbol) {
                        'flame' => 'danger',
                        'flame-over-circle' => 'danger',
                        'gas-cylinder' => 'warning',
                        'skull-crossbones' => 'danger',
                        'corrosion' => 'danger',
                        'health-hazard' => 'warning',
                        'environment' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn ($record): string => match ($record->chemical->whmisHazardClass->symbol) {
                        'flame' => 'heroicon-m-fire',
                        'flame-over-circle' => 'heroicon-m-fire',
                        'gas-cylinder' => 'heroicon-m-beaker',
                        'skull-crossbones' => 'heroicon-m-exclamation-triangle',
                        'corrosion' => 'heroicon-m-bolt',
                        'health-hazard' => 'heroicon-m-heart',
                        'environment' => 'heroicon-m-globe-alt',
                        default => 'heroicon-m-minus',
                    }),
                TextColumn::make('storageCabinet.location.user.name')
                    ->label('Supervisor'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                DeleteAction::make()
                    ->before(function ($record, DeleteAction $action) {
                        $storageCabinetId = $this->getOwnerRecord()->id; // Get the storage cabinet ID
                        $hasOngoingReconciliation = Reconciliation::where('storage_cabinet_id', $storageCabinetId)
                            ->where('status', 'ongoing')
                            ->exists();

                        if ($hasOngoingReconciliation) {
                            Notification::make()
                                ->title('Action Blocked')
                                ->body('Cannot delete a container because a reconciliation is ongoing for this storage cabinet.')
                                ->warning()
                                ->send();
                            $action->cancel();
                        }

                        return true;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
