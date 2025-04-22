<?php

namespace App\Livewire;

use App\Models\Reconciliation;
use App\Models\ReconciliationItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class ViewReconciliation extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public int $reconciliation_id;

    public function mount($reconciliation_id): void
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
        $reconciliation = Reconciliation::find($this->reconciliation_id);
        $isStopped = $reconciliation && $reconciliation->status === 'stopped';

        return $table
            ->query(ReconciliationItem::with('container')->where('reconciliation_id', $this->reconciliation_id))
            ->columns([
                TextColumn::make('container.StorageCabinet.name')
                    ->label('Storage Cabinet')
                    ->searchable()
                    ->sortable(),
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
            ->groups([
                Group::make('container.storageCabinet.name')
                    ->label('Storage Cabinet')
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
                        $reconciliation = Reconciliation::find($this->reconciliation_id);

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
                        $reconciliation = Reconciliation::find($this->reconciliation_id);

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
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if (! empty($state)) {
                                    $this->processBarcode($state);
                                    $set('barcode', '');
                                }
                            }),
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
                        $unreconciledCount = ReconciliationItem::where('status', 'pending')->count();

                        if ($unreconciledCount > 0) {
                            ReconciliationItem::where('status', 'pending')->update([
                                'status' => $data['action'],
                            ]);

                            $actionLabel = $data['action'] === 'reconciled' ? 'reconciled' : 'marked as missing';

                        }
                        Notification::make()
                            ->title('Reconciliation Completed')
                            ->body("Successfully $actionLabel $unreconciledCount items.")
                            ->success()
                            ->send();
                        $reconciliation = Reconciliation::find($this->reconciliation_id);
                        if ($reconciliation) {
                            $reconciliation->update([
                                'status' => 'completed',
                                'ended_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }),
            ]);
    }

    protected function processBarcode(string $barcode): void
    {
        $reconciliationItem = ReconciliationItem::where('reconciliation_id', $this->reconciliation_id)
            ->whereHas('container', function ($query) use ($barcode) {
                $query->where('barcode', $barcode);
            })
            ->with(['container.location'])
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

    public function render(): View
    {
        return view('livewire.list-reconciliation-items');
    }
}
