<?php

namespace App\Filament\Resources\ReconciliationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReconciliationItemRelationManager extends RelationManager
{
    protected static string $relationship = 'reconciliationItems';

    public function form(Form $form): Form
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('container.barcode'),
                TextColumn::make('container.chemical.cas')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect(),
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
