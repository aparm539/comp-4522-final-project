<?php

namespace App\Filament\Resources\StorageCabinetResource\RelationManagers;

use App\Models\Reconciliation;
use App\Models\StorageCabinet;
use Filament\Forms;
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
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
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
                TextColumn::make('chemical.ishazardous')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => match ($state) {
                        1 => 'danger',
                        0 => '-',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'danger',
                        default => 'primary'
                    })
                    ->label('Hazardous'),
                TextColumn::make('storageCabinet.location.user.name')
                    ->label('Supervisor'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->before(function ($record, $data, CreateAction $action) {
                        $storageCabinetId = $this->getOwnerRecord()->id; // Get the storage cabinet ID
                        $hasOngoingReconciliation = Reconciliation::where('storage_cabinet_id', $storageCabinetId)
                            ->where('status', 'ongoing')
                            ->exists();

                        if ($hasOngoingReconciliation) {
                            Notification::make()
                                ->title('Action Blocked')
                                ->body('Cannot add a container because a reconciliation is ongoing for this storage cabinet.')
                                ->warning()
                                ->send();
                            $action->cancel();

                        }

                        return true;
                    }),
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
