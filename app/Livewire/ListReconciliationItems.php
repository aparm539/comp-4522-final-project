<?php

namespace App\Livewire;

use App\Filament\Resources\ReconciliationItemResource\Pages\CreateReconciliationItem;
use App\Models\ReconciliationItem;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class ListReconciliationItems extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(ReconciliationItem::query())
            ->columns([
                TextColumn::make('container.barcode'),
                TextColumn::make('container.chemical.cas')
                    ->label('CAS #'),
                TextColumn::make('container.chemical.name')
                    ->limit(25),
                IconColumn::make('is_reconciled')
                    ->boolean(),

            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('Reconcile')
                    ->label('Reconcile')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function (ReconciliationItem $record) {
                        if (!$record->is_reconciled) {
                            $record->update(['is_reconciled' => true]);
                            Notification::make()
                                ->title('Reconciliation successful')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Already reconciled')
                                ->warning()
                                ->send();
                        }
                    }),

            ])
            ->bulkActions([
                // ...
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('Reconcile by Barcode')
                    ->label('Reconcile Barcode')
                    ->form([
                        TextInput::make('barcode')
                            ->label('Container Barcode')
                            ->placeholder('Enter the container barcode...')
                            ->required(),
                    ])
                    ->action(function (array $data) { // Receive the form data
                        // Find the reconciliation item by container barcode
                        $record = ReconciliationItem::whereHas('container', function ($query) use ($data) {
                            $query->where('barcode', $data['barcode']);
                        })->first();

                        if (!$record) {
                            Notification::make()
                                ->title('Reconciliation Failed')
                                ->body('No record found for the provided barcode.')
                                ->warning()
                                ->send();
                            return;
                        }

                        if (!$record->is_reconciled) {
                            $record->update(['is_reconciled' => true]);
                            Notification::make()
                                ->title('Reconciliation Successful')
                                ->body('The container has been reconciled successfully.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Already Reconciled')
                                ->body('This container is already reconciled.')
                                ->warning()
                                ->send();
                        }
                    }),
            ]);

    }
   // public function reconcileContainer()
    public function render()
    {
        return view('livewire.list-reconciliation-items');
    }
}
