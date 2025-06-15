<?php

namespace App\Filament\Resources\ChemicalResource;

use App\Models\Chemical;
use App\Models\User;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class ChemicalTable
{
    public static function columns(): array
    {
        return [
            TextColumn::make('cas')
                ->label('CAS #')
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->searchable()
                ->sortable(),
            TextColumn::make('whmisHazardClass.class_name')
                ->label('Hazard Class')
                ->badge()
                ->color(fn (Chemical $record): string => match ($record->whmisHazardClass->symbol) {
                    'flame' => 'danger',
                    'flame-over-circle' => 'danger',
                    'gas-cylinder' => 'warning',
                    'skull-crossbones' => 'danger',
                    'corrosion' => 'danger',
                    'health-hazard' => 'warning',
                    'environment' => 'success',
                    default => 'gray',
                })
                ->icon(fn (Chemical $record): string => match ($record->whmisHazardClass->symbol) {
                    'flame' => 'heroicon-m-fire',
                    'flame-over-circle' => 'heroicon-m-fire',
                    'gas-cylinder' => 'heroicon-m-beaker',
                    'skull-crossbones' => 'heroicon-m-exclamation-triangle',
                    'corrosion' => 'heroicon-m-bolt',
                    'health-hazard' => 'heroicon-m-heart',
                    'environment' => 'heroicon-m-globe-alt',
                    default => 'heroicon-m-minus',
                })
                ->searchable()
                ->sortable(),
        ];
    }

    public static function actions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public static function headerActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public static function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()
                ->visible(function (Chemical $chemical): bool {
                    /** @var User|null $user */
                    $user = auth()->user();

                    return $user?->can('delete', $chemical) ?? false;
                }),
        ];
    }
}
