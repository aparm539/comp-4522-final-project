<?php

namespace App\Filament\Resources\StorageCabinetResource;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class StorageCabinetTable
{
    public static function columns(): array
    {
        return [
            TextColumn::make('location.user.name')
                ->Label('Supervisor'),
            TextColumn::make('location.room_number'),
            TextColumn::make('name')
                ->Label('Storage Cabinet'),
        ];
    }

    public static function actions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public static function bulkActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')),
            ]),
        ];
    }
}
