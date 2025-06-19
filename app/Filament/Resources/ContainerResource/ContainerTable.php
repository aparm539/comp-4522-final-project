<?php

namespace App\Filament\Resources\ContainerResource;

use App\Models\Container;
use App\Models\Lab;
use App\Models\StorageLocation;
use App\Models\User;
use App\Services\Container\ContainerExporter;
use App\Services\Container\ContainerImporter;
use App\Services\Container\ContainerService;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ContainerTable
{
    /**
     * Placeholder buttons to show in toolbar when no records selected.
     *
     * @return array<int, array{label:string,color:string,icon?:string}>
     */
    public static function placeholderBulkButtons(): array
    {
        return [
            [
                'label' => __('filament-actions::delete.multiple.label'),
                'color' => 'danger',
                'icon' => 'heroicon-m-trash',
            ],
            [
                'label' => 'Change Lab',
                'color' => 'primary',
                'icon' => 'heroicon-o-map',
            ],
        ];
    }

    public static function columns(): array
    {

        return [
            TextColumn::make('barcode')
                ->sortable()
                ->searchable()
                ->width('100px'),
            TextColumn::make('chemical.cas')
                ->label('CAS #')
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
                ->formatStateUsing(fn ($state, $record) => $state.' '.$record->unitofmeasure->abbreviation)
                ->sortable()
                ->searchable()
                ->width('80px'),
            TextColumn::make('storageLocation.lab.room_number')
                ->label('Lab')
                ->formatStateUsing(fn ($state, $record) => $state.' - '.$record->storageLocation->name)
                ->sortable()
                ->searchable()
                ->width('180px'),
            ViewColumn::make('hazard_icons')
                ->label('Hazards')
                ->tooltip(fn ($record): string => $record->chemical->whmisHazardClasses->pluck('class_name')->join(', '))
                ->view('filament.tables.columns.container-hazard-icons')
                ->searchable(false)
                ->sortable(false)
                ->width('120px'),

        ];
    }

    public static function filters(): array
    {
        return [
            SelectFilter::make('chemical')
                ->relationship('chemical', 'name')
                ->searchable()
                ->preload(),

            Filter::make('location')
                ->label('Lab & Storage Location')
                ->form([
                    Select::make('lab_id')
                        ->label('Lab')
                        ->options(fn (): array => Lab::query()->pluck('room_number', 'id')->all())
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('storage_location_id', null)),
                    Select::make('storage_location_id')
                        ->label('Storage Location')
                        ->options(function (callable $get): array {
                            $labId = $get('lab_id');

                            $query = StorageLocation::query();

                            if (filled($labId)) {
                                $query->where('lab_id', $labId);
                            }

                            return $query->pluck('name', 'id')->all();
                        })
                        ->disabled(fn (callable $get) => blank($get('lab_id')))
                        ->searchable(),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['lab_id'] ?? null, function (Builder $query, $labId): Builder {
                            return $query->whereHas('storageLocation.lab', fn (Builder $q) => $q->where('id', $labId));
                        })
                        ->when($data['storage_location_id'] ?? null, fn (Builder $query, $storageLocationId): Builder => $query->where('storage_location_id', $storageLocationId));
                }),

            SelectFilter::make('unit_of_measure')
                ->relationship('unitOfMeasure', 'abbreviation')
                ->searchable()
                ->preload(),
        ];
    }

    public static function actions(): array
    {
        return [
            EditAction::make()
                ->slideOver()
                ->modalContentFooter(fn (Container $record) => view(view: 'containers.footer-component', data: ['containerId' => 1]))
                ->mutateFormDataUsing(function (array $data): array {
                    $data['last_edit_author_id'] = auth()?->user()?->id;

                    return $data;
                })
                ->mutateRecordDataUsing(function (array $data): array {
                    $container = Container::findOrFail($data['id'])->load('storageLocation.lab');
                    $containerLab = $container->storageLocation->lab;
                    $data['lab_id'] = $containerLab->id;

                    return $data;
                }),

        ];
    }

    public static function headerActions(): array
    {
        return [
            CreateAction::make(),
            ExportAction::make()
                ->exporter(ContainerExporter::class),
            ImportAction::make()
                ->importer(ContainerImporter::class)
                ->visible(function () {
                    /** @var User $user */
                    $user = auth()->user();

                    return $user->can('create', Container::class);
                }),
            Action::make('print')
                ->label('Print All Containers')
                ->icon('heroicon-o-printer')
                ->action(function () {
                    return app(ContainerService::class)->printContainers();
                }),
        ];
    }

    public static function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()
                ->visible(function (Container $container): bool {
                    /** @var User $user */
                    $user = auth()->user();

                    return $user->can('delete', $container);
                }),
            BulkAction::make('changeLab')
                ->label('Change Lab')
                ->icon('heroicon-o-map')
                ->visible(function (Container $container): bool {
                    /** @var User $user */
                    $user = auth()->user();

                    return $user->can('update', $container);
                })
                ->form([
                    Select::make('lab_id')
                        ->label('Lab')
                        ->options(function () {
                            return Lab::all()->mapWithKeys(function ($lab) {
                                return [$lab->id => $lab->room_number];
                            });
                        })
                        ->disableOptionWhen(function ($value) {
                            /** @var Lab $lab */
                            $lab = Lab::find($value);

                            return $lab && $lab->hasOngoingReconciliation();
                        })
                        ->helperText(function () {
                            $ongoingLabs = app(ContainerService::class)->getUnavailableLabs();

                            if ($ongoingLabs->isNotEmpty()) {
                                $labs = $ongoingLabs->pluck('room_number')->join(', ');

                                return "The following labs are currently being reconciled and cannot be selected: $labs";
                            }

                            return null;
                        })
                        ->searchable()
                        ->required(),
                    Select::make('storage_location_id')
                        ->label('Storage Location')
                        ->options(function ($get) {
                            return StorageLocation::where('lab_id', $get('lab_id'))
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->required(),
                ])
                ->action(fn (Collection $records, array $data) => app(ContainerService::class)->changeLab($records, $data))
                ->requiresConfirmation()
                ->modalHeading('Change Lab')
                ->modalDescription('Are you sure you want to change the lab and storage location of the selected containers?')
                ->modalSubmitActionLabel('Yes, change lab'),
        ];
    }
}
