<?php

namespace App\Filament\Resources\LocationResource;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class LocationTable
{
    public static function columns(): array
    {
        return [
            TextColumn::make('room_number'),
            TextColumn::make('barcode'),
            TextColumn::make('description')
                ->limit(20),
            TextColumn::make('user.name')
                ->label('Supervisor'),
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
