<?php

namespace App\Filament\Resources\ContainerResource;

use App\Models\Chemical;
use App\Models\Location;
use App\Models\StorageCabinet;
use App\Models\UnitOfMeasure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

class ContainerForm
{
    public static function make(): array
    {
        return [
            Select::make('chemical_id')
                ->label('CAS #')
                ->options(Chemical::all()->pluck('cas', 'id'))
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

        ];
    }
}
