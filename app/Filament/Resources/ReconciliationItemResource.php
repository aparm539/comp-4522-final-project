<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReconciliationItemResource\Pages;
use App\Filament\Resources\ReconciliationItemResource\RelationManagers;
use App\Models\ReconciliationItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReconciliationItemResource extends Resource
{
    protected static ?string $model = ReconciliationItem::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('reconciliation_id')
                    ->relationship('reconciliation', 'id')
                    ->required(),
                Forms\Components\Select::make('container_id')
                    ->relationship('container', 'id')
                    ->required(),
                Forms\Components\TextInput::make('expected_quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('actual_quantity')
                    ->numeric(),
                Forms\Components\Toggle::make('is_reconciled')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reconciliation.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('container.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('actual_quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_reconciled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),

            ])
            ->headeractions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReconciliationItems::route('/'),
            'create' => Pages\CreateReconciliationItem::route('/create'),
            'edit' => Pages\EditReconciliationItem::route('/{record}/edit'),
        ];
    }
}
