<?php

namespace App\Filament\Resources\ReconciliationResource\Pages;

use App\Filament\Resources\ReconciliationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReconciliation extends ViewRecord
{
    protected static string $resource = ReconciliationResource::class;
    protected static string $view = 'filament.resources.reconciliation.pages.view-reconciliation';
}
