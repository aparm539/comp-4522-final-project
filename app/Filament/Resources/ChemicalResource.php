<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChemicalResource\Pages;
use App\Filament\Resources\ChemicalResource\RelationManagers;
use App\Models\Chemical;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChemicalResource extends Resource
{
    protected static ?string $model = Chemical::class;

    protected static ?string $navigationIcon = 'fluentui-beaker-16-o';
    protected static ?string $activeNavigationIcon = 'fluentui-beaker-16';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('cas')->required(),
                TextInput::make('name')->required(),
                Toggle::make('ishazardous')
                    ->default(true)
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cas')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')),
                ]),
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
            'index' => Pages\ListChemicals::route('/'),
            'create' => Pages\CreateChemical::route('/create'),
        ];
    }
}
