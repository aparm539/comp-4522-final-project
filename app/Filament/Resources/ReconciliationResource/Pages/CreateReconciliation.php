<?php

namespace App\Filament\Resources\ReconciliationResource\Pages;

use App\Filament\Resources\ReconciliationResource\ReconciliationResource;
use App\Models\Lab;
use App\Models\Reconciliation;
use App\Models\ReconciliationItem;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateReconciliation extends CreateRecord
{
    public static string $resource = ReconciliationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $labIds = $data['lab_ids'] ?? null;

        // If lab_ids is present, we're creating multiple reconciliations
        if ($labIds && is_array($labIds) && !empty($labIds)) {
            return $this->createMultipleReconciliations($data);
        }

        // Fall back to single reconciliation creation (backwards compatibility)
        return parent::handleRecordCreation($data);
    }

    protected function createMultipleReconciliations(array $data): Model
    {
        $labIds = $data['lab_ids'];
        $useSameSettings = $data['use_same_settings'] ?? true;
        $labConfigurations = $data['lab_configurations'] ?? [];

        // Validate no labs have ongoing reconciliations
        $labsWithOngoing = Lab::whereIn('id', $labIds)
            ->whereHas('reconciliations', function ($query) {
                $query->where('status', 'ongoing');
            })
            ->get();

        if ($labsWithOngoing->isNotEmpty()) {
            $labNames = $labsWithOngoing->pluck('room_number')->join(', ');
            Notification::make()
                ->danger()
                ->title('Cannot create reconciliations')
                ->body("The following labs already have ongoing reconciliations: $labNames")
                ->send();

            throw new \Illuminate\Validation\ValidationException(
                validator: \Illuminate\Support\Facades\Validator::make([], []),
                errorBag: 'default'
            );
        }

        $createdCount = 0;

        DB::transaction(function () use ($labIds, $useSameSettings, $labConfigurations, $data, &$createdCount) {
            foreach ($labIds as $labId) {
                $lab = Lab::findOrFail($labId);

                // Determine settings for this lab
                if ($useSameSettings) {
                    $status = $data['status'] ?? 'ongoing';
                    $notes = $data['notes'] ?? null;
                    $startedAt = $data['started_at'] ?? ($status === 'ongoing' ? now('America/Edmonton') : null);
                    $endedAt = $data['ended_at'] ?? ($status === 'completed' ? now('America/Edmonton') : null);
                } else {
                    $labConfig = collect($labConfigurations)->firstWhere('lab_id', $labId);
                    if (!$labConfig) {
                        continue; // Skip if no configuration found for this lab
                    }
                    $status = $labConfig['status'] ?? 'ongoing';
                    $notes = $labConfig['notes'] ?? null;
                    $startedAt = $labConfig['started_at'] ?? ($status === 'ongoing' ? now('America/Edmonton') : null);
                    $endedAt = $labConfig['ended_at'] ?? ($status === 'completed' ? now('America/Edmonton') : null);
                }

                // Convert dates to Carbon instances if they're strings
                if ($startedAt && is_string($startedAt)) {
                    $startedAt = Carbon::parse($startedAt, 'America/Edmonton');
                }
                if ($endedAt && is_string($endedAt)) {
                    $endedAt = Carbon::parse($endedAt, 'America/Edmonton');
                }

                // Create reconciliation
                $reconciliation = new Reconciliation;
                $reconciliation->status = $status;
                $reconciliation->notes = $notes;
                $reconciliation->lab_id = $labId;
                $reconciliation->started_at = $startedAt;
                $reconciliation->ended_at = $endedAt;
                $reconciliation->save();

                // Generate reconciliation items for all containers in this lab
                foreach ($lab->storageLocations as $storageLocation) {
                    foreach ($storageLocation->containers as $container) {
                        $reconciliationItem = new ReconciliationItem;
                        $reconciliationItem->container()->associate($container);
                        $reconciliationItem->reconciliation()->associate($reconciliation);
                        $reconciliationItem->save();
                    }
                }

                $createdCount++;
            }
        });

        // Set record to the last created one (for compatibility with afterCreate hooks)
        $this->record = Reconciliation::whereIn('lab_id', $labIds)->latest()->first();

        // Show success notification
        Notification::make()
            ->success()
            ->title('Reconciliations created')
            ->body("Successfully created {$createdCount} reconciliation(s).")
            ->send();

        return $this->record;
    }

    protected function afterCreate(): void
    {
        // Only run if we have a valid record with lab_id (single creation path)
        if ($this->record && $this->record->lab_id) {
            $reconciliation = $this->record;
            $lab = Lab::find($reconciliation->lab_id);

            if ($lab) {
                foreach ($lab->storageLocations as $storageLocation) {
                    foreach ($storageLocation->containers as $container) {
                        $reconciliationItem = new ReconciliationItem;
                        $reconciliationItem->container()->associate($container);
                        $reconciliationItem->reconciliation()->associate($reconciliation);
                        $reconciliationItem->save();
                    }
                }
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Create')
                ->disabled(function () {
                    // Check if we have lab_ids selected (which means we're on step 2)
                    // If lab_ids is empty or not set, we're on step 1
                    $data = $this->form->getRawState();
                    $labIds = $data['lab_ids'] ?? null;
                    
                    // Disable if no labs selected (still on first step)
                    return empty($labIds);
                }),
        ];
    }
}
