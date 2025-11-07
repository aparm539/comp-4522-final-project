<?php

namespace App\Filament\Resources\ReconciliationResource;

use App\Models\Lab;
use App\Models\Reconciliation;
use App\Models\ReconciliationItem;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;

class ReconciliationTable
{
    public static function columns(): array
    {
        return [
            TextColumn::make('lab.room_number')
                ->label('Lab')
                ->formatStateUsing(fn ($state, $record) => $record->lab->room_number),
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
            Group::make('lab.room_number')
                ->collapsible(),
        ];
    }

    public static function actions(): array
    {
        return [
            // No actions - clicking row navigates to view page
        ];
    }

    public static function headerActions(): array
    {
        return [
            Action::make('Create Reconciliations for All Labs')
                ->action(fn () => self::createReconciliationsForAllLabs()),
            CreateAction::make(),
        ];
    }

    public static function bulkActions(): array
    {
        return [
            DeleteBulkAction::make(),
        ];
    }

    public static function createReconciliationsForAllLabs(): void
    {
        foreach (Lab::all() as $lab) {
            $reconciliation = new Reconciliation;
            $reconciliation->status = 'ongoing';
            $reconciliation->started_at = now();
            $reconciliation->lab_id = $lab->id;
            $reconciliation->save();

            // Get all containers from all storage locations in this lab
            foreach ($lab->storageLocations as $storageLocation) {
                foreach ($storageLocation->containers as $container) {
                    $reconciliationItem = new ReconciliationItem;
                    $reconciliationItem->container()->associate($container);
                    $reconciliationItem->reconciliation()->associate($reconciliation);
                    $reconciliationItem->save();
                }
            }
        }
    }
}
