<?php

namespace App\Livewire;

use App\Filament\Resources\ReconciliationItemResource\Pages\CreateReconciliationItem;
use App\Models\ReconciliationItem;
use App\Models\Location;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
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

    public $reconciliation_id;
    public $newLocationId;
    public $modalData = [
        'reconciliationItemId' => null,
        'currentLocation' => null,
        'containerName' => null,
    ];

    public function mount($reconciliation_id)
    {
        $this->reconciliation_id = $reconciliation_id;
    }

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
                TextInput::make('expected_quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('actual_quantity')
                    ->numeric(),
                Toggle::make('is_reconciled')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        $reconciliation = \App\Models\Reconciliation::find($this->reconciliation_id);
        $isStopped = $reconciliation && $reconciliation->status === 'stopped';

        return $table
            ->query(ReconciliationItem::where('reconciliation_id', $this->reconciliation_id))
            ->columns([
                TextColumn::make('container.barcode'),
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
            ->filters([
                // ...
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
                                'is_reconciled' => true
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
                                'is_reconciled' => true
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
            ->headerActions([
                Action::make('Pause Reconciliation')
                    ->label('Pause Reconciliation')
                    ->icon('heroicon-o-pause')
                    ->requiresConfirmation()
                    ->modalHeading('Pause Reconciliation')
                    ->modalDescription('Are you sure you want to pause this reconciliation? You can resume it later.')
                    ->disabled($isStopped)
                    ->visible(!$isStopped)
                    ->action(function () {
                        $reconciliation = \App\Models\Reconciliation::find($this->reconciliation_id);
                        
                        if ($reconciliation) {
                            $reconciliation->update([
                                'status' => 'stopped',
                                'ended_at' => now()
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
                        $reconciliation = \App\Models\Reconciliation::find($this->reconciliation_id);
                        
                        if ($reconciliation) {
                            $reconciliation->update([
                                'status' => 'ongoing',
                                'ended_at' => null
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
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if (!empty($state)) {
                                    $this->processBarcode($state);
                                    $set('barcode', '');
                                }
                            }),
                    ])
                    ->disabled($isStopped)
                    ->action(function (array $data) {
                        if (!empty($data['barcode'])) {
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
                        $unreconciledCount = ReconciliationItem::where('status', 'pending')->count();
                        
                        if ($unreconciledCount > 0) {
                            ReconciliationItem::where('status', 'pending')->update([
                                'status' => $data['action'],
                                'is_reconciled' => true
                            ]);
                            
                            $actionLabel = $data['action'] === 'reconciled' ? 'reconciled' : 'marked as missing';
                            Notification::make()
                                ->title('Reconciliation Completed')
                                ->body("Successfully {$actionLabel} {$unreconciledCount} items.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('No Items to Process')
                                ->body('All items have already been processed.')
                                ->info()
                                ->send();
                        }
                    }),
            ]);
    }

    protected function processBarcode(string $barcode): void
    {
        $record = ReconciliationItem::where('reconciliation_id', $this->reconciliation_id)
            ->whereHas('container', function ($query) use ($barcode) {
                $query->where('barcode', $barcode);
            })
            ->with(['container.location'])
            ->first();

        if (!$record) {
            Notification::make()
                ->title('Reconciliation Failed')
                ->body('No record found for the provided barcode in this reconciliation.')
                ->warning()
                ->send();
            return;
        }

        if ($record->status === 'pending') {
            // Get the current location
            $currentLocation = $record->container->location_id;
            
            // Find a different location to move to
            $newLocation = Location::where('id', '!=', $currentLocation)->first();
            
            if ($newLocation) {
                // Update container location
                $record->container->update(['location_id' => $newLocation->id]);
                
                // Update reconciliation item
                $record->update([
                    'status' => 'reconciled',
                    'is_reconciled' => true,
                    'location_id' => $newLocation->id
                ]);

                // Refresh the relationships
                $record->load(['container.location']);

                Notification::make()
                    ->title('Location Updated')
                    ->body("Container moved from {$record->container->location->name} to {$newLocation->name}")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('No Alternative Location')
                    ->body('No alternative location found to move the container to.')
                    ->warning()
                    ->send();
            }
        } else {
            Notification::make()
                ->title('Already Processed')
                ->body('This container has already been processed.')
                ->warning()
                ->send();
        }
    }

    public function updateLocation(): void
    {
        if (!$this->newLocationId) {
            Notification::make()
                ->title('Location Required')
                ->body('Please select a new location.')
                ->warning()
                ->send();
            return;
        }

        $record = ReconciliationItem::findOrFail($this->modalData['reconciliationItemId']);
        
        if ($record->status === 'pending') {
            // Update container location
            $record->container->update(['location_id' => $this->newLocationId]);
            
            // Update reconciliation item
            $record->update([
                'status' => 'reconciled',
                'is_reconciled' => true,
                'location_id' => $this->newLocationId
            ]);

            $this->newLocationId = null;
            $this->modalData = [
                'reconciliationItemId' => null,
                'currentLocation' => null,
                'containerName' => null,
            ];

            Notification::make()
                ->title('Location Updated')
                ->body('The container has been moved to the new location and marked as reconciled.')
                ->success()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.list-reconciliation-items');
    }
}
