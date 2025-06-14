<?php

namespace App\Filament\Resources\ReconciliationResource\Pages;

use App\Filament\Resources\ReconciliationResource\ReconciliationResource;
use App\Models\Lab;
use App\Models\ReconciliationItem;
use Filament\Resources\Pages\CreateRecord;

class CreateReconciliation extends CreateRecord
{
    public static string $resource = ReconciliationResource::class;

    protected function afterCreate(): void
    {
        $reconciliation = $this->record;
        $lab = Lab::find($reconciliation->lab_id);

        foreach ($lab->storageCabinets as $storageCabinet) {
            foreach ($storageCabinet->containers as $container) {
                $reconciliationItem = new ReconciliationItem;
                $reconciliationItem->container()->associate($container);
                $reconciliationItem->reconciliation()->associate($reconciliation);
                $reconciliationItem->save();
            }
        }

    }
}
