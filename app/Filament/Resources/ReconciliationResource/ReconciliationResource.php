<?php

namespace App\Filament\Resources\ReconciliationResource;

use App\Models\Reconciliation;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ReconciliationResource extends Resource
{
    protected static ?string $model = Reconciliation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $activeNavigationIcon = 'heroicon-s-clipboard-document-list';

    protected static string $view = 'filament.resources.reconciliation.pages.view-reconciliation';

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
            'view' => Pages\ViewReconciliation::route('/{record}'),
        ];
    }
}
