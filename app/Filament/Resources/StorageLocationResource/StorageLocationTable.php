<?php

namespace App\Filament\Resources\StorageLocationResource;

use App\Models\StorageLocation;
use App\Models\User;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class StorageLocationTable
{
    public static function columns(): array
    {
        return [

            TextColumn::make('lab.room_number')
                ->sortable(),
            TextColumn::make('name')
                ->Label('Storage Location')
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
        return [CreateAction::make()];
    }

    public static function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()
                ->visible(function (StorageLocation $storageLocation) {
                    /** @var User $user */
                    $user = auth()->user();

                    return $user->can('delete', $storageLocation);
                }),
        ];
    }
}
