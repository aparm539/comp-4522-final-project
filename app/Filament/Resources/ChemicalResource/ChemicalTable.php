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
            TextColumn::make('hazard_classes')
                ->label('Hazard Classes')
                ->getStateUsing(fn (Chemical $record): string => $record->whmisHazardClasses->pluck('class_name')->join(', '))
                ->badge()
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
                    /** @var User|null $user */
                    $user = auth()->user();

                    return $user?->can('delete', $chemical) ?? false;
                }),
        ];
    }
}
