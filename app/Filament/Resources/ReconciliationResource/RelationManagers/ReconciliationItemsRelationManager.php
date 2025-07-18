<?php

namespace App\Filament\Resources\ReconciliationResource\RelationManagers;

use App\Models\ReconciliationItem;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\ReconciliationResource\Pages\ListReconciliations;



class ReconciliationItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'reconciliationItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('reconciliation_id')
                    ->relationship('reconciliation', 'id')
                    ->required(),
                Select::make('container_id')
                    ->relationship('container', 'id')
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reconciled' => 'Reconciled',
                        'missing' => 'Missing',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        $reconciliation = $this->getOwnerRecord();
        $isStopped = $reconciliation && $reconciliation->status === 'stopped';

        return $table
            ->columns([
                TextColumn::make('container.storageLocation.name')
                    ->label('Storage Location')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('container.barcode')
                    ->label('Barcode'),
                TextColumn::make('container.chemical.cas')
                    ->label('CAS #'),
                TextColumn::make('container.chemical.name')
                    ->limit(25),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'reconciled' => 'success',
                        'missing' => 'danger',
                    }),
            ])
            ->groups([
                Group::make('container.storageLocation.name')
                    ->label('Storage Location')
                    ->collapsible(),
            ])
            ->actions([
                Action::make('Reconcile')
                    ->label('Reconcile')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->disabled($isStopped)
                    ->action(function (ReconciliationItem $record) {
                        if ($record->status === 'pending') {
                            $record->update([
                                'status' => 'reconciled',
                            ]);
                            Notification::make()
                                ->title('Reconciliation successful')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Already processed')
                                ->warning()
                                ->send();
                        }
                    }),
                Action::make('Mark Missing')
                    ->label('Mark Missing')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->disabled($isStopped)
                    ->action(function (ReconciliationItem $record) {
                        if ($record->status === 'pending') {
                            $record->update([
                                'status' => 'missing',
                            ]);
                            Notification::make()
                                ->title('Marked as missing')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Already processed')
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                // ...
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'pending' => 'Pending',
                        'reconciled' => 'Reconciled',
                        'missing' => 'Missing',
                    ]),
            ])
            ->headerActions([
                Action::make('Pause Reconciliation')
                    ->label('Pause Reconciliation')
                    ->icon('heroicon-o-pause')
                    ->requiresConfirmation()
                    ->modalHeading('Pause Reconciliation')
                    ->modalDescription('Are you sure you want to pause this reconciliation? You can resume it later.')
                    ->disabled($isStopped)
                    ->visible(! $isStopped)
                    ->action(function () {
                        $reconciliation = $this->getOwnerRecord();

                        if ($reconciliation) {
                            $reconciliation->update([
                                'status' => 'stopped',
                                'ended_at' => now(),
                            ]);

                            Notification::make()
                                ->title('Reconciliation Paused')
                                ->body('The reconciliation has been paused. You can resume it later.')
                                ->success()
                                ->send();
                        }
                    }),
                Action::make('Resume Reconciliation')
                    ->label('Resume Reconciliation')
                    ->icon('heroicon-o-play')
                    ->requiresConfirmation()
                    ->modalHeading('Resume Reconciliation')
                    ->modalDescription('Are you sure you want to resume this reconciliation?')
                    ->visible($isStopped)
                    ->action(function () {
                        $reconciliation = $this->getOwnerRecord();

                        if ($reconciliation) {
                            $reconciliation->update([
                                'status' => 'ongoing',
                                'ended_at' => null,
                            ]);

                            Notification::make()
                                ->title('Reconciliation Resumed')
                                ->body('The reconciliation has been resumed. You can now continue reconciling items.')
                                ->success()
                                ->send();
                        }
                    }),
                Action::make('Reconcile by Barcode')
                    ->label('Reconcile Barcode')
                    ->form([
                        TextInput::make('barcode')
                            ->label('Container Barcode')
                            ->placeholder('Scan barcode or press Enter...')
                            ->required()
                            ->live(),
                    ])
                    ->disabled($isStopped)
                    ->action(function (array $data) {
                        if (! empty($data['barcode'])) {
                            $this->processBarcode($data['barcode']);
                        }
                    }),
                Action::make('Complete Reconciliation')
                    ->label('Complete Reconciliation')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Complete Reconciliation')
                    ->modalDescription('How would you like to handle the remaining unreconciled items?')
                    ->disabled($isStopped)
                    ->form([
                        Select::make('action')
                            ->label('Action for remaining items')
                            ->options([
                                'reconciled' => 'Mark all as Reconciled',
                                'missing' => 'Mark all as Missing',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $unreconciledCount = ReconciliationItem::where('reconciliation_id', $this->getOwnerRecord()->id)
                            ->where('status', 'pending')
                            ->count();
                        $actionLabel = '';

                        if ($unreconciledCount > 0) {
                            ReconciliationItem::where('reconciliation_id', $this->getOwnerRecord()->id)
                                ->where('status', 'pending')
                                ->update([
                                    'status' => $data['action'],
                                ]);

                            $actionLabel = $data['action'] === 'reconciled' ? 'reconciled' : 'marked as missing';

                        }
                        Notification::make()
                            ->title('Reconciliation Completed')
                            ->body("Successfully $actionLabel $unreconciledCount items.")
                            ->success()
                            ->send();
                        $reconciliation = $this->getOwnerRecord();
                        if ($reconciliation) {
                            $reconciliation->update([
                                'status' => 'completed',
                                'ended_at' => date('Y-m-d H:i:s'),
                            ]);
                        }

                        return redirect()->to(ListReconciliations::getUrl());
                    }),
            ]);
    }

    protected function processBarcode(string $barcode): void
    {
            $reconciliationItem = ReconciliationItem::where('reconciliation_id', $this->getOwnerRecord()->id)
            ->whereHas('container', function ($query) use ($barcode) {
                $query->where('barcode', $barcode);
            })
            ->with(['container.lab'])
            ->first();

        if (! $reconciliationItem) {
            Notification::make()
                ->title('Reconciliation Failed')
                ->body('No record found for the provided barcode in this reconciliation.')
                ->warning()
                ->send();

            return;
        }

        if ($reconciliationItem->status === 'pending') {

            $reconciliationItem->update([
                'status' => 'reconciled',
            ]);

            Notification::make()
                ->title('Reconciliation successful')
                ->success()
                ->send();
        }

    }
}