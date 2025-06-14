<?php

namespace App\Filament\Resources\StorageLocationResource\RelationManagers;

use App\Filament\Resources\ContainerResource\ContainerResource;
use App\Models\Chemical;
use App\Models\Reconciliation;
use App\Models\UnitOfMeasure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
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
