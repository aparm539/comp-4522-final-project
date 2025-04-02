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
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('stopped'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                textColumn::make('storageCabinet.name')
                    ->label('Location')
                    ->formatStateUsing(fn ($state, $record) => ($record->storageCabinet->location->room_number.' '.$record->storageCabinet->name)),
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
                Group::make('storageContainer.location.room_number')
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
                Action::make('Create Reconciliations for All Shelves')
                    ->action(fn() => self::createReconciliationsForAllShelves()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function createReconciliationsForAllShelves(){
        foreach(StorageCabinet::all() as $storageCabinet){
            $reconciliation = new Reconciliation();
            $reconciliation->status = 'ongoing';
            $reconciliation->storage_cabinet_id = $storageCabinet->id;
            $reconciliation->save();
            foreach($storageCabinet->containers as $container){
                $reconciliationItem = new ReconciliationItem();
                $reconciliationItem->container()->associate($container);
                $reconciliationItem->reconciliation()->associate($reconciliation);
                $reconciliationItem->is_reconciled = false;
                $reconciliationItem->expected_quantity= 1;
                $reconciliationItem->actual_quantity=0;
                $reconciliationItem->save();
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
