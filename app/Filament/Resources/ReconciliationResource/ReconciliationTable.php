<?php

namespace App\Filament\Resources\ReconciliationResource;

use App\Models\Location;
use App\Models\Reconciliation;
use App\Models\ReconciliationItem;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;

class ReconciliationTable
{
    public static function columns(): array
    {
        return [
            TextColumn::make('location.room_number')
                ->label('Location')
                ->formatStateUsing(fn ($state, $record) => $record->location->room_number),
            IconColumn::make('status')
                ->icon(fn (string $state): string => match ($state) {
                    'stopped' => 'heroicon-o-no-symbol',
                    'ongoing' => 'heroicon-o-pencil',
                    'completed' => 'heroicon-o-check-circle',
                    'canceled' => 'heroicon-o-exclamation-circle',
                })
                ->color(fn (string $state): string => match ($state) {
                    'stopped' => 'danger',
                    'ongoing' => 'warning',
                    'completed' => 'success',
                    default => 'gray',
                }),
            TextColumn::make('started_at')
                ->dateTime()
                ->sortable(),
            TextColumn::make('ended_at')
                ->dateTime()
                ->sortable(),
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function groups(): array
    {
        return [
            Group::make('status')
                ->collapsible(),
            Group::make('location.room_number')
                ->collapsible(),
        ];
    }

    public static function actions(): array
    {
        return [
            EditAction::make(),
            ViewAction::make(),
        ];
    }

    public static function headerActions(): array
    {
        return [
            Action::make('Create Reconciliations for All Locations')
                ->action(fn () => self::createReconciliationsForAllLocations()),
        ];
    }

    public static function bulkActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ];
    }

    public static function createReconciliationsForAllLocations(): void
    {
        foreach (Location::all() as $location) {
            $reconciliation = new Reconciliation;
            $reconciliation->status = 'ongoing';
            $reconciliation->started_at = now();
            $reconciliation->location_id = $location->id;
            $reconciliation->save();


            // Get all containers from all storage cabinets in this location
            foreach ($location->storageCabinets as $storageCabinet) {
                foreach ($storageCabinet->containers as $container) {
                    $reconciliationItem = new ReconciliationItem;
                    $reconciliationItem->container()->associate($container);
                    $reconciliationItem->reconciliation()->associate($reconciliation);
                    $reconciliationItem->save();
                }
            }
        }
    }
}
