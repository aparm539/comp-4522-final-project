<?php

namespace App\Filament\Widgets;

use App\Models\Chemical;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ChemicalsToOrder extends BaseWidget
{
    protected static ?int $sort = 0;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Chemical::query()->doesntHave('containers') // Filters chemicals with no containers
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Chemical Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cas')
                    ->label('CAS #')
                    ->sortable()
                    ->searchable(),

            ]);
    }
}
