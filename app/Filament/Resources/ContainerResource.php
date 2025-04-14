<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContainerResource\Pages;
use App\Models\Container;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Filament\Resources\ContainerResource\ContainerForm;
use App\Filament\Resources\ContainerResource\ContainerTable;

class ContainerResource extends Resource
{
    protected static ?string $model = Container::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $activeNavigationIcon = 'heroicon-s-rectangle-stack';



    public static function form(Form $form): Form
    {
        return $form
            ->schema(ContainerForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(ContainerTable::columns())
            ->filters(ContainerTable::filters())
            ->actions(ContainerTable::actions())
            ->headerActions(ContainerTable::headerActions())
            ->bulkActions(ContainerTable::bulkActions());
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
            'index' => Pages\ListContainers::route('/'),
        ];
    }
}
