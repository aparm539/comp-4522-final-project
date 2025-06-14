<?php

namespace App\Services\Container;

use App\Models\Chemical;
use App\Models\Container;
use App\Models\Lab;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ContainerImporter extends Importer
{
    protected static ?string $model = Container::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('barcode')
                ->label('Barcode'),
            ImportColumn::make('chemical.cas')
                ->label('CAS #')
                ->requiredMapping(),
            ImportColumn::make('quantity')
                ->label('Quantity')
                ->requiredMapping(),
            ImportColumn::make('unitofmeasure.abbreviation')
                ->label('Unit')
                ->requiredMapping(),
            ImportColumn::make('storageLocation.lab.room_number')
                ->label('Room')
                ->requiredMapping(),
            ImportColumn::make('storageLocation.name')
                ->label('Location')
                ->requiredMapping(),
        ];
    }

    public function resolveRecord(): ?Container
    {
        $container = new Container;

        // Find or create related models
        $chemical = Chemical::where('cas', $this->data['chemical.cas'])->first();
        if (! $chemical) {
            return null;
        }

        $unitOfMeasure = UnitOfMeasure::where('abbreviation', $this->data['unitofmeasure.abbreviation'])->first();
        if (! $unitOfMeasure) {
            return null;
        }

        $lab = Lab::where('room_number', $this->data['storageLocation.lab.room_number'])->first();
        if (! $lab) {
            return null;
        }

        $storageLocation = StorageLocation::where('name', $this->data['storageLocation.name'])
            ->where('lab_id', $lab->id)
            ->first();
        if (! $storageLocation) {
            return null;
        }

        // Set the relationships
        $container->chemical_id = $chemical->id;
        $container->unit_of_measure_id = $unitOfMeasure->id;
        $container->storage_location_id = $storageLocation->id;

        // Set other attributes
        $container->quantity = $this->data['quantity'];
        $container->barcode = $this->data['barcode'] ?? null;

        return $container;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your container import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
