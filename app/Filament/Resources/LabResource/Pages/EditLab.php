<?php

namespace App\Filament\Resources\LabResource\Pages;

use App\Filament\Resources\LabResource\LabResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLab extends EditRecord
{
    protected static string $resource = LabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
