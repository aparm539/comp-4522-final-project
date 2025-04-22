<?php

namespace App\Filament\Resources\ReconciliationResource\Pages;

use App\Filament\Resources\ReconciliationResource\ReconciliationResource;
use App\Models\Location;
use App\Models\ReconciliationItem;
use Filament\Resources\Pages\CreateRecord;

class CreateReconciliation extends CreateRecord
{
    public static string $resource = ReconciliationResource::class;

    protected function afterCreate(): void
    {
        $reconciliation = $this->record;
        $location = Location::find($reconciliation->location_id);

        foreach ($location->storageCabinets as $storageCabinet) {
            foreach ($storageCabinet->containers as $container) {
                $reconciliationItem = new ReconciliationItem;
                $reconciliationItem->container()->associate($container);
                $reconciliationItem->reconciliation()->associate($reconciliation);
                $reconciliationItem->save();
            }
        }

    }
}
