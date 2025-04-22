<?php

namespace App\Filament\Resources\ChemicalResource;

use App\Models\Chemical;
use App\Models\User;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class ChemicalTable
{
    public static function columns(): array
    {
        return [
            TextColumn::make('cas')->searchable()->sortable(),
            TextColumn::make('name')->searchable()->sortable(),
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
                    ->visible(function (Chemical $chemical) {
                        /** @var User $user */
                        $user = auth()->user();

                        return $user->can('delete', $chemical);
                    }),
            ]),
        ];
    }
}
