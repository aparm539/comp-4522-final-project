<?php

namespace App\Services\Container;

use App\Models\Chemical;
use App\Models\Container;
use App\Models\Lab;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Auth;

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
                    ->requiredMapping()
                    ->fillRecordUsing(fn ($state) => null)
                    ->ignoreBlankState()
                    ->helperText('Used to match an existing chemical; not written during import.'),
            ImportColumn::make('chemical.whmisHazardClasses.class_name')
                ->label('WHMIS Hazard Classes')
                ->array(',')
                ->fillRecordUsing(fn ($state) => null)
                ->ignoreBlankState()
                ->helperText('Optional; manage hazard classes from the chemical record.'),
            ImportColumn::make('quantity')
                ->label('Quantity')
                ->requiredMapping(),
            ImportColumn::make('unitofmeasure.abbreviation')
                ->label('Unit')
                    ->requiredMapping()
                    ->fillRecordUsing(fn ($state) => null)
                    ->ignoreBlankState()
                    ->helperText('Used to match a unit of measure; not written during import.'),
            ImportColumn::make('storageLocation.lab.room_number')
                ->label('Room')
                    ->requiredMapping()
                    ->fillRecordUsing(fn ($state) => null)
                    ->ignoreBlankState()
                    ->helperText('Used to locate the lab; not written during import.'),
            ImportColumn::make('storageLocation.name')
                ->label('Location')
                    ->requiredMapping()
                    ->fillRecordUsing(fn ($state) => null)
                    ->ignoreBlankState()
                    ->helperText('Used to locate the storage location; not written during import.'),
            ImportColumn::make('lastEditAuthor.name')
                ->label('Last Edited By')
                ->fillRecordUsing(fn ($state) => null)
                ->ignoreBlankState()
                ->helperText('Ignored during import; the current user is recorded automatically.'),
        ];
    }

    public function resolveRecord(): ?Container
    {
        $container = new Container;

        $chemicalCas = $this->data['chemical.cas'] ?? null;
        $unitAbbreviation = $this->data['unitofmeasure.abbreviation'] ?? null;
        $labRoomNumber = $this->data['storageLocation.lab.room_number'] ?? null;
        $storageLocationName = $this->data['storageLocation.name'] ?? null;

        $chemical = Chemical::where('cas', $chemicalCas)->first();
        if (! $chemical) {
            throw new RowImportFailedException(match (true) {
                blank($chemicalCas) => 'Unable to import container row: CAS # value is missing.',
                default => "Unable to import container row: no chemical was found with CAS # [{$chemicalCas}].",
            });
        }

        $unitOfMeasure = UnitOfMeasure::where('abbreviation', $unitAbbreviation)->first();
        if (! $unitOfMeasure) {
            throw new RowImportFailedException(match (true) {
                blank($unitAbbreviation) => 'Unable to import container row: unit value is missing.',
                default => "Unable to import container row: no unit of measure was found with abbreviation [{$unitAbbreviation}].",
            });
        }

        $lab = Lab::where('room_number', $labRoomNumber)->first();
        if (! $lab) {
            throw new RowImportFailedException(match (true) {
                blank($labRoomNumber) => 'Unable to import container row: room value is missing.',
                default => "Unable to import container row: no lab was found with room number [{$labRoomNumber}].",
            });
        }

        $storageLocation = StorageLocation::where('name', $storageLocationName)
            ->where('lab_id', $lab->id)
            ->first();
        if (! $storageLocation) {
            throw new RowImportFailedException(match (true) {
                blank($storageLocationName) => 'Unable to import container row: storage location value is missing.',
                default => "Unable to import container row: no storage location named [{$storageLocationName}] exists in lab [{$lab->room_number}].",
            });
        }

        // Set the relationships
        $container->chemical_id = $chemical->id;
        $container->unit_of_measure_id = $unitOfMeasure->id;
        $container->storage_location_id = $storageLocation->id;

        // Set other attributes
        $container->quantity = $this->data['quantity'];
        $container->barcode = $this->data['barcode'] ?? null;
        $container->last_edit_author_id = $this->resolveLastEditAuthorId();

        return $container;
    }

    protected function resolveLastEditAuthorId(): ?int
    {
        if ($userId = Auth::id()) {
            return $userId;
        }

        $importUser = $this->getImport()->user;

        return $importUser?->getAuthIdentifier();
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
