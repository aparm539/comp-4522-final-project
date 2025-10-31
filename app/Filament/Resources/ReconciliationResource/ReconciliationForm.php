<?php

namespace App\Filament\Resources\ReconciliationResource;

use App\Models\Lab;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

class ReconciliationForm
{
    public static function make(?string $context = null): array
    {
        // Only show wizard on create
        if ($context !== 'create') {
            return static::makeSimpleForm();
        }

        return [
            Wizard::make([
                Step::make('Select Labs')
                    ->schema([
                        Select::make('lab_ids')
                            ->label('Labs')
                            ->options(function () {
                                return Lab::all()->pluck('room_number', 'id');
                            })
                            ->multiple()
                            ->required()
                            ->searchable()
                            ->live()
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
                            }),
                    ]),
                Step::make('Configure Reconciliations')
                    ->schema([
                        Checkbox::make('use_same_settings')
                            ->label('Use same settings for all labs')
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(function ($set, $state, $get, $component) {
                                // When unchecked, populate the repeater with items for all selected labs
                                if ($state === false) {
                                    $labIds = $get('lab_ids') ?? [];
                                    if (!empty($labIds)) {
                                        $items = [];
                                        foreach ($labIds as $labId) {
                                            $items[] = [
                                                'lab_id' => $labId,
                                                'status' => 'ongoing',
                                                'notes' => null,
                                                'started_at' => Carbon::now('America/Edmonton')->format('Y-m-d H:i:s'),
                                                'ended_at' => null,
                                            ];
                                        }
                                        $set('lab_configurations', $items);
                                    }
                                }
                            })
                            ->columnSpanFull(),
                        Select::make('status')
                            ->options([
                                'ongoing' => 'Ongoing',
                                'completed' => 'Completed',
                                'stopped' => 'Stopped',
                            ])
                            ->required()
                            ->default('ongoing')
                            ->visible(fn ($get) => $get('use_same_settings') === true)
                            ->afterStateUpdated(function ($set, $state) {
                                if ($state === 'ongoing') {
                                    $set('started_at', Carbon::now('America/Edmonton'));
                                }
                                if ($state === 'completed') {
                                    $set('ended_at', Carbon::now('America/Edmonton'));
                                } else {
                                    $set('ended_at', null);
                                }
                            }),
                        Textarea::make('notes')
                            ->columnSpanFull()
                            ->visible(fn ($get) => $get('use_same_settings') === true),
                        DateTimePicker::make('started_at')
                            ->hidden(fn ($get) => $get('status') !== 'ongoing' || $get('use_same_settings') !== true)
                            ->required(fn ($get) => $get('status') === 'ongoing' && $get('use_same_settings') === true)
                            ->default(fn () => Carbon::now('America/Edmonton'))
                            ->timezone('America/Edmonton')
                            ->seconds(false)
                            ->visible(fn ($get) => $get('use_same_settings') === true),
                        DateTimePicker::make('ended_at')
                            ->hidden(fn ($get) => $get('status') !== 'completed' || $get('use_same_settings') !== true)
                            ->timezone('America/Edmonton')
                            ->seconds(false)
                            ->visible(fn ($get) => $get('use_same_settings') === true),
                        Repeater::make('lab_configurations')
                            ->schema([
                                Select::make('lab_id')
                                    ->label('Lab')
                                    ->options(function ($get) {
                                        $selectedLabIds = $get('../../lab_ids') ?? [];
                                        return Lab::whereIn('id', $selectedLabIds)
                                            ->pluck('room_number', 'id');
                                    })
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
                                Select::make('status')
                                    ->options([
                                        'ongoing' => 'Ongoing',
                                        'completed' => 'Completed',
                                        'stopped' => 'Stopped',
                                    ])
                                    ->required()
                                    ->default('ongoing')
                                    ->live()
                                    ->afterStateUpdated(function ($set, $state) {
                                        if ($state === 'ongoing') {
                                            $set('started_at', Carbon::now('America/Edmonton'));
                                            $set('ended_at', null);
                                        }
                                        if ($state === 'completed') {
                                            $set('ended_at', Carbon::now('America/Edmonton'));
                                        }
                                        if ($state === 'stopped') {
                                            $set('ended_at', null);
                                        }
                                    }),
                                Textarea::make('notes')
                                    ->columnSpanFull(),
                                DateTimePicker::make('started_at')
                                    ->hidden(fn ($get) => $get('status') !== 'ongoing')
                                    ->required(fn ($get) => $get('status') === 'ongoing')
                                    ->default(fn () => Carbon::now('America/Edmonton'))
                                    ->timezone('America/Edmonton')
                                    ->seconds(false),
                                DateTimePicker::make('ended_at')
                                    ->hidden(fn ($get) => $get('status') !== 'completed')
                                    ->timezone('America/Edmonton')
                                    ->seconds(false),
                            ])
                            ->columns(2)
                            ->visible(fn ($get) => $get('use_same_settings') !== true)
                            ->defaultItems(0)
                            ->addable(false)
                            ->reorderable(false)
                            ->deletable(false)
                            ->live()
                            ->afterStateHydrated(function ($component, $state, $get) {
                                $useSameSettings = $get('use_same_settings');
                                if ($useSameSettings === true) {
                                    $component->state([]);
                                    return;
                                }
                                
                                $labIds = $get('lab_ids') ?? [];
                                if (empty($labIds)) {
                                    $component->state([]);
                                    return;
                                }
                                
                                // Only populate if state is empty or doesn't match lab_ids
                                $currentLabIds = collect($state ?? [])->pluck('lab_id')->toArray();
                                sort($labIds);
                                sort($currentLabIds);
                                
                                if ($labIds !== $currentLabIds || empty($state)) {
                                    $items = [];
                                    foreach ($labIds as $labId) {
                                        $existingItem = collect($state ?? [])->firstWhere('lab_id', $labId);
                                        if ($existingItem) {
                                            $items[] = $existingItem;
                                        } else {
                                            $items[] = [
                                                'lab_id' => $labId,
                                                'status' => 'ongoing',
                                                'notes' => null,
                                                'started_at' => Carbon::now('America/Edmonton')->format('Y-m-d H:i:s'),
                                                'ended_at' => null,
                                            ];
                                        }
                                    }
                                    $component->state($items);
                                }
                            })
                            ->afterStateUpdated(function ($component, $state, $get) {
                                $useSameSettings = $get('use_same_settings');
                                if ($useSameSettings === true) {
                                    return;
                                }
                                
                                $labIds = $get('lab_ids') ?? [];
                                if (empty($labIds)) {
                                    $component->state([]);
                                    return;
                                }
                                
                                $currentLabIds = collect($state ?? [])->pluck('lab_id')->toArray();
                                sort($labIds);
                                sort($currentLabIds);
                                
                                if ($labIds !== $currentLabIds) {
                                    $items = [];
                                    foreach ($labIds as $labId) {
                                        $existingItem = collect($state ?? [])->firstWhere('lab_id', $labId);
                                        if ($existingItem) {
                                            $items[] = $existingItem;
                                        } else {
                                            $items[] = [
                                                'lab_id' => $labId,
                                                'status' => 'ongoing',
                                                'notes' => null,
                                                'started_at' => Carbon::now('America/Edmonton')->format('Y-m-d H:i:s'),
                                                'ended_at' => null,
                                            ];
                                        }
                                    }
                                    $component->state($items);
                                }
                            })
                            ->dehydrated(),
                    ]),
            ])
                ->columnSpanFull()
                ->persistStepInQueryString(false),
        ];
    }

    protected static function makeSimpleForm(): array
    {
        return [
            Select::make('status')
                ->options([
                    'ongoing' => 'Ongoing',
                    'completed' => 'Completed',
                    'stopped' => 'Stopped',
                ])
                ->required()
                ->default('ongoing')
                ->afterStateUpdated(function ($set, $state) {
                    if ($state === 'ongoing') {
                        $set('started_at', Carbon::now('America/Edmonton'));
                    }
                    if ($state === 'completed') {
                        $set('ended_at', Carbon::now('America/Edmonton'));
                    } else {
                        $set('ended_at', null);
                    }
                }),
            Textarea::make('notes')
                ->columnSpanFull(),
            DateTimePicker::make('started_at')
                ->hidden(fn ($get) => $get('status') !== 'ongoing')
                ->required()
                ->default(fn () => Carbon::now('America/Edmonton'))
                ->timezone('America/Edmonton')
                ->seconds(false),
            DateTimePicker::make('ended_at')
                ->hidden(fn ($get) => $get('status') !== 'completed')
                ->timezone('America/Edmonton')
                ->seconds(false),
            Select::make('lab_id')
                ->label('Lab')
                ->options(Lab::all()->pluck('room_number', 'id'))
                ->required(),
        ];
    }
}
