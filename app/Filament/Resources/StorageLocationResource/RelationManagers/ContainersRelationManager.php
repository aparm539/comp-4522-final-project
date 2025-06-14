<?php

namespace App\Filament\Resources\StorageLocationResource\RelationManagers;

use App\Models\Chemical;
use App\Models\Reconciliation;
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
                TextColumn::make('storageLocation')
                    ->formatStateUsing(fn ($state, $record) => ($record->storageLocation->lab->room_number.' '.$record->storageLocation->name))
                    ->label('Lab'),
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
                TextColumn::make('storageLocation.lab.user.name')
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
                        $storageLocationId = $this->getOwnerRecord()->id; // Get the storage location ID
                        $hasOngoingReconciliation = Reconciliation::where('storage_location_id', $storageLocationId)
                            ->where('status', 'ongoing')
                            ->exists();

                        if ($hasOngoingReconciliation) {
                            Notification::make()
                                ->title('Action Blocked')
                                ->body('Cannot delete a container because a reconciliation is ongoing for this storage Location.')
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
