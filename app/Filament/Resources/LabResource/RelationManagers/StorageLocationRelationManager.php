<?php

namespace App\Filament\Resources\LabResource\RelationManagers;

use App\Filament\Resources\StorageLocationResource\StorageLocationResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class StorageLocationRelationManager extends RelationManager
{
    protected static string $relationship = 'storageLocations';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return StorageLocationResource::form($form);
    }

    public function table(Table $table): Table
    {
        return StorageLocationResource::table($table);

    }
}
