<?php

namespace App\Filament\Resources\ReconciliationResource;

use App\Models\Reconciliation;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Filament\Resources\ReconciliationResource\RelationManagers\ReconciliationItemsRelationManager;

class ReconciliationResource extends Resource
{
    protected static ?string $model = Reconciliation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $activeNavigationIcon = 'heroicon-s-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(ReconciliationForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(ReconciliationTable::columns())
            ->groups(ReconciliationTable::groups())
            ->actions(ReconciliationTable::actions())
            ->headerActions(ReconciliationTable::headerActions())
            ->bulkActions(ReconciliationTable::bulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReconciliations::route('/'),
            'create' => Pages\CreateReconciliation::route('/create'),
            'view' => Pages\ViewReconciliation::route('/{record}'),

        ];
    }

    public static function getRelations(): array
    {
        return [
            ReconciliationItemsRelationManager::class,
        ];
    }
}
