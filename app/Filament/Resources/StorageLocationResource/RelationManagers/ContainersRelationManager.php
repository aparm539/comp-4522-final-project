<?php

namespace App\Filament\Resources\StorageLocationResource\RelationManagers;

use App\Filament\Resources\ContainerResource\ContainerResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ContainersRelationManager extends RelationManager
{
    protected static string $relationship = 'containers';

    public function form(Form $form): Form
    {
        return ContainerResource::form($form);
    }

    public function table(Table $table): Table
    {
        return ContainerResource::table($table);
    }
}
