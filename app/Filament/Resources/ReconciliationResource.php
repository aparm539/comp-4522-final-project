<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReconciliationResource\Pages;
use App\Filament\Resources\ReconciliationResource\RelationManagers;
use App\Filament\Resources\ReconciliationResource\RelationManagers\ReconciliationItemRelationManager;
use App\Models\Location;
use App\Models\Reconciliation;
use App\Models\ReconciliationItem;
use App\Models\StorageCabinet;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReconciliationResource extends Resource
{
    protected static ?string $model = Reconciliation::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $activeNavigationIcon = 'heroicon-s-rectangle-stack';

    protected static string $view = 'filament.resources.reconciliation.pages.view-reconciliation';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                        'canceled' => 'Canceled',
                        'stopped' => 'Stopped',
                    ])
                    ->required()
                    ->default('ongoing')
                    ->afterStateUpdated(function ( $set, $state) {
                        if ($state === 'ongoing') {
                            $set('started_at', now());
                        }
                        if ($state === 'completed') {
                            $set('ended_at', now());
                        }
                        else{
                            $set('ended_at', null);
                        }


                    }),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                DateTimePicker::make('started_at')
                    ->hidden(fn ($get) => $get('status') !== 'ongoing')
                    ->required(),
                DateTimePicker::make('ended_at')
                    ->hidden(fn ($get) => $get('status') !== 'completed')
            ])
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                textColumn::make('location.room_number')
                    ->label('Location')
                    ->formatStateUsing(fn ($state, $record) => $record->location->room_number),
                IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'stopped' => 'akar-stop',
                        'ongoing' => 'heroicon-o-pencil',
                        'completed' => 'heroicon-o-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'stopped' => 'danger',
                        'ongoing' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ended_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('status')
                    ->collapsible(),
                Group::make('location.room_number')
                    ->collapsible(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->headerActions([
                Action::make('Create Reconciliations for All Locations')
                    ->action(fn() => self::createReconciliationsForAllLocations()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function createReconciliationsForAllLocations(){
        foreach(Location::all() as $location){
            $reconciliation = new Reconciliation();
            $reconciliation->status = 'ongoing';
            $reconciliation->started_at = now();
            $reconciliation->location_id = $location->id;
            $reconciliation->save();
            
            // Get all containers from all storage cabinets in this location
            foreach($location->storageCabinets as $storageCabinet) {
                foreach($storageCabinet->containers as $container){
                    $reconciliationItem = new ReconciliationItem();
                    $reconciliationItem->container()->associate($container);
                    $reconciliationItem->reconciliation()->associate($reconciliation);
                    $reconciliationItem->is_reconciled = false;
                    $reconciliationItem->expected_quantity = 1;
                    $reconciliationItem->actual_quantity = 0;
                    $reconciliationItem->save();
                }
            }
        }
    }

    public static function getRelations(): array
    {
        return [
            ReconciliationItemRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReconciliations::route('/'),
            'create' => Pages\CreateReconciliation::route('/create'),
            'edit' => Pages\EditReconciliation::route('/{record}/edit'),
            'view' => Pages\ViewReconciliation::route('/{record}')
        ];
    }
}
