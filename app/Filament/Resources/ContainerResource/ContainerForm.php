<?php

namespace App\Filament\Resources\ContainerResource;

use App\Models\Chemical;
use App\Models\Lab;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;

class ContainerForm
{
    public static function make(): array
    {
        return [
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
            Select::make('lab_id')
                ->label('Lab')
                ->dehydrated(false)
                ->options(function () {
                    return Lab::all()->pluck('room_number', 'id');
                })
                ->disableOptionWhen(function ($value) {
                    $lab = Lab::find($value);

                    return $lab && $lab->hasOngoingReconciliation();
                })
                ->helperText(function () {
                    $ongoingLabs = Lab::whereHas('reconciliations', function ($query) {
                        $query->where('status', 'ongoing');
                    })->get();

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
                    $lab = $get('lab_id');
                    if ($lab) {
                        return StorageLocation::where('lab_id', $lab)->pluck('name', 'id');
                    }

                    return StorageLocation::all()->pluck('name', 'id');
                })
                ->required()
                ->searchable(),
            TextInput::make('barcode')
                ->label('Barcode')
                ->required(),
            Hidden::make('last_edit_author_id')
                ->label('Last Edit Author')
                ->required()
                ->default(auth()?->user()?->id),

        ];
    }
}
