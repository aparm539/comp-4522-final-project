<?php

namespace App\Filament\Resources\ContainerResource;

use App\Models\Container;
use App\Models\Location;
use App\Models\StorageCabinet;
use App\Models\User;
use App\Services\Container\ContainerExporter;
use App\Services\Container\ContainerImporter;
use App\Services\Container\ContainerService;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Collection;

class ContainerTable
{
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
                ->formatStateUsing(fn ($state, $record) => $state.' - '.$record->storageCabinet->name)
                ->sortable()
                ->searchable()
                ->width('180px'),
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
                })
                ->sortable()
                ->searchable()
                ->width('120px'),
            TextColumn::make('storageCabinet.location.user.name')
                ->label('Supervisor')
                ->sortable()
                ->searchable()
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
            SelectFilter::make('location')
                ->relationship('storageCabinet.location', 'room_number')
                ->searchable()
                ->preload(),
            SelectFilter::make('storage_cabinet')
                ->relationship('storageCabinet', 'name')
                ->searchable()
                ->preload(),
            SelectFilter::make('unit_of_measure')
                ->relationship('unitOfMeasure', 'abbreviation')
                ->searchable()
                ->preload(),
            SelectFilter::make('hazard_class')
                ->relationship('chemical.whmisHazardClass', 'class_name')
                ->searchable()
                ->preload(),
        ];
    }

    public static function actions(): array
    {
        return [
            EditAction::make()
                ->slideOver()
                ->mutateRecordDataUsing(function (array $data): array {
                    $container = Container::findOrFail($data['id'])->load('storageCabinet.location');
                    $containerLocation = $container->storageCabinet->location;
                    $data['location_id'] = $containerLocation->id;

                    return $data;
                }),
        ];
    }

    public static function headerActions(): array
    {
        return [
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
            BulkAction::make('changeLocation')
                ->label('Change Location')
                ->icon('heroicon-o-map')
                ->visible(function (Container $container): bool {
                    /** @var User $user */
                    $user = auth()->user();

                    return $user->can('update', $container);
                })
                ->form([
                    Select::make('location_id')
                        ->label('Location')
                        ->options(function () {
                            return Location::all()->mapWithKeys(function ($location) {
                                return [$location->id => $location->room_number];
                            });
                        })
                        ->disableOptionWhen(function ($value) {
                            /** @var Location $location */
                            $location = Location::find($value);

                            return $location && $location->hasOngoingReconciliation();
                        })
                        ->helperText(function () {
                            $ongoingLocations = app(ContainerService::class)->getUnavailableLocations();

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
                            return StorageCabinet::where('location_id', $get('location_id'))
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->required(),
                ])
                ->action(fn (Collection $records, array $data) => app(ContainerService::class)->changeLocation($records, $data))
                ->requiresConfirmation()
                ->modalHeading('Change Location')
                ->modalDescription('Are you sure you want to change the location and storage cabinet of the selected containers?')
                ->modalSubmitActionLabel('Yes, change location'),
        ];
    }
}
