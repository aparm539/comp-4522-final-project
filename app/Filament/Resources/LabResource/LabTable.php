<?php

namespace App\Filament\Resources\LabResource;

use App\Models\Lab;
use App\Models\User;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class LabTable
{
    public static function columns(): array
    {
        return [
            TextColumn::make('room_number'),
            TextColumn::make('description')
                ->limit(20),

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
                ->visible(
                    function (Lab $lab) {
                        /** @var User $user */
                        $user = auth()->user();

                        return $user->can('delete', $lab);
                    }),
        ];
    }
}
