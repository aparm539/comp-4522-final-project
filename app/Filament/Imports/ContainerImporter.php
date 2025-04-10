<?php

namespace App\Filament\Imports;

use App\Models\Container;
use App\Models\Chemical;
use App\Models\UnitOfMeasure;
use App\Models\Location;
use App\Models\StorageCabinet;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\ImportColumn;

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
            ImportColumn::make('storageCabinet.location.room_number')
                ->label('Room')
                ->requiredMapping(),
            ImportColumn::make('storageCabinet.name')
                ->label('Cabinet')
                ->requiredMapping(),
        ];
    }

    public static function getColumnLabels(): array
    {
        return [
            'barcode' => 'Barcode',
            'chemical.cas' => 'CAS #',
            'quantity' => 'Quantity',
            'unitofmeasure.abbreviation' => 'Unit',
            'storageCabinet.location.room_number' => 'Room',
            'storageCabinet.name' => 'Cabinet',
        ];
    }

    public function resolveRecord(): ?Container
    {
        $container = new Container();
        
        // Find or create related models
        $chemical = Chemical::where('cas', $this->data['chemical.cas'])->first();
        if (!$chemical) {
            return null;
        }
        
        $unitOfMeasure = UnitOfMeasure::where('abbreviation', $this->data['unitofmeasure.abbreviation'])->first();
        if (!$unitOfMeasure) {
            return null;
        }
        
        $location = Location::where('room_number', $this->data['storageCabinet.location.room_number'])->first();
        if (!$location) {
            return null;
        }
        
        $storageCabinet = StorageCabinet::where('name', $this->data['storageCabinet.name'])
            ->where('location_id', $location->id)
            ->first();
        if (!$storageCabinet) {
            return null;
        }
        
        // Set the relationships
        $container->chemical_id = $chemical->id;
        $container->unit_of_measure_id = $unitOfMeasure->id;
        $container->storage_cabinet_id = $storageCabinet->id;
        
        // Set other attributes
        $container->quantity = $this->data['quantity'];
        $container->barcode = $this->data['barcode'] ?? null;
        
        return $container;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your container import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
} 