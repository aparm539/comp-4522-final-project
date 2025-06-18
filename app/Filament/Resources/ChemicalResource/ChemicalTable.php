<?php

namespace App\Filament\Resources\ChemicalResource;

use App\Models\Chemical;
use App\Models\User;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;

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
            ViewColumn::make('hazard_icons')
                ->label('Hazards')
                ->tooltip(fn (Chemical $record): string => $record->whmisHazardClasses->pluck('class_name')->join(', '))
                ->view('filament.tables.columns.chemical-hazard-icons')
                ->searchable(false)
                ->sortable(false),
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
                    /** @var \App\Models\User|null $user */
                    $user = auth()->user();

                    return $user?->can('delete', $chemical) ?? false;
                }),
        ];
    }
}
