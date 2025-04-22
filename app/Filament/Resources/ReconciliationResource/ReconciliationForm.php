<?php

namespace App\Filament\Resources\ReconciliationResource;

use App\Models\Location;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class ReconciliationForm
{
    public static function make(): array
    {
        return [
            Select::make('status')
                ->options([
                    'ongoing' => 'Ongoing',
                    'completed' => 'Completed',
                    'canceled' => 'Canceled',
                    'stopped' => 'Stopped',
                ])
                ->required()
                ->default('ongoing')
                ->afterStateUpdated(function ($set, $state) {
                    if ($state === 'ongoing') {
                        $set('started_at', now());
                    }
                    if ($state === 'completed') {
                        $set('ended_at', now());
                    } else {
                        $set('ended_at', null);
                    }
                }),
            Textarea::make('notes')
                ->columnSpanFull(),
            DateTimePicker::make('started_at')
                ->hidden(fn ($get) => $get('status') !== 'ongoing')
                ->date('started_at')
                ->required()
                ->closeOnDateSelection(),
            DateTimePicker::make('ended_at')
                ->hidden(fn ($get) => $get('status') !== 'completed')
                ->date('ended_at')
                ->closeOnDateSelection(),
            Select::make('location_id')
                ->label('Location')
                ->options(Location::all()->pluck('room_number', 'id'))
                ->required(),
        ];
    }
}
