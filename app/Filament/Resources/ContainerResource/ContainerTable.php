<?php

namespace App\Filament\Resources\ContainerResource;

use App\Models\Container;
use App\Models\Location;
use App\Models\StorageCabinet;
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
use Illuminate\Database\Eloquent\Builder;
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
                ->label('Hazardous')
                ->sortable()
                ->searchable()
                ->width('100px'),
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
            SelectFilter::make('hazardous')
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
        ];
    }

    public static function actions(): array
    {
        return [
            EditAction::make()->slideOver(),
        ];
    }

    public static function headerActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(ContainerExporter::class),
            ImportAction::make()
                ->importer(ContainerImporter::class)
                ->visible(fn () => auth()->user()->can('create', Container::class)),
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
                ->visible(fn (Container $container): bool => auth()->user()->can('delete', $container)),
            BulkAction::make('changeLocation')
                ->label('Change Location')
                ->icon('heroicon-o-map')
                ->visible(fn (Container $container): bool => auth()->user()->can('update', $container))
                ->form([
                    Select::make('location_id')
                        ->label('Location')
                        ->options(function () {
                            return Location::all()->mapWithKeys(function ($location) {
                                return [$location->id => $location->room_number];
                            });
                        })
                        ->disableOptionWhen(function ($value) {
                            $location = Location::class->find($value);

                            return $location && $location->hasOngoingReconciliation();
                        })
                        ->helperText(function () {
                            $ongoingLocations = app(ContainerService::class)->getUnavailableLocations();

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
                        ->options(function ($get) {
                            return StorageCabinet::where('location_id', $get('location_id'))
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->required(),
                ])
                ->action(function (Collection $records, array $data) {
                    return app(ContainerService::class)->changeLocation($records, $data);
                })
                ->requiresConfirmation()
                ->modalHeading('Change Location')
                ->modalDescription('Are you sure you want to change the location and storage cabinet of the selected containers?')
                ->modalSubmitActionLabel('Yes, change location'),
        ];
    }
}
