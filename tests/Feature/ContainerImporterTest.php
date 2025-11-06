<?php

use App\Enums\WhmisPictogram;
use App\Models\Chemical;
use App\Models\Container;
use App\Models\Lab;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use App\Models\User;
use App\Models\WhmisHazardClass;
use App\Services\Container\ContainerImporter;
use Filament\Actions\Imports\Models\Import as ImportModel;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function createImportForUser(User $user): ImportModel {
    return ImportModel::create([
        'user_id' => $user->id,
        'file_name' => 'containers.csv',
        'file_path' => 'containers.csv',
        'importer' => ContainerImporter::class,
        'processed_rows' => 0,
        'successful_rows' => 0,
        'total_rows' => 1,
    ]);
}

function defaultColumnMap(): array {
    return [
        'barcode' => 'Barcode',
        'chemical.cas' => 'CAS #',
        'chemical.whmisHazardClasses.class_name' => 'WHMIS Hazard Classes',
        'quantity' => 'Quantity',
        'unitofmeasure.abbreviation' => 'Unit',
        'storageLocation.lab.room_number' => 'Room',
        'storageLocation.name' => 'Location',
        'lastEditAuthor.name' => 'Last Edited By',
    ];
}

it('imports a container row using exporter columns', function () {
    $user = User::factory()->create();
    auth()->login($user);

    $hazardClass = WhmisHazardClass::firstOrCreate(
        ['class_name' => 'Flammable'],
        [
            'description' => 'Highly flammable material',
            'symbol' => WhmisPictogram::FLAME,
        ]
    );

    $chemical = Chemical::factory()->create([
        'cas' => '67-64-1',
        'name' => 'Acetone',
    ]);
    $chemical->whmisHazardClasses()->sync([$hazardClass->id]);

    $unit = UnitOfMeasure::firstOrCreate(
        ['abbreviation' => 'L'],
        ['name' => 'Litres']
    );

    $lab = Lab::factory()->create([
        'room_number' => 'B203',
    ]);

    $location = StorageLocation::factory()
        ->for($lab)
        ->create([
            'name' => 'Shelf 2',
        ]);

    $import = createImportForUser($user);
    $importer = $import->getImporter(defaultColumnMap(), []);

    $barcode = 'MRUC92PH8W';
    $hazardNames = $chemical->whmisHazardClasses->pluck('class_name')->join(', ');

    $row = [
        'Barcode' => $barcode,
        'CAS #' => $chemical->cas,
        'WHMIS Hazard Classes' => $hazardNames,
        'Quantity' => '314.39',
        'Unit' => $unit->abbreviation,
        'Room' => $lab->room_number,
        'Location' => $location->name,
        'Last Edited By' => $user->name,
    ];

    ($importer)($row);

    $container = Container::where('barcode', $barcode)->first();

    expect($container)->not()->toBeNull();
    expect($container->chemical_id)->toEqual($chemical->id);
    expect($container->unit_of_measure_id)->toEqual($unit->id);
    expect($container->storage_location_id)->toEqual($location->id);
    expect($container->quantity)->toEqual(314.39);
    expect($container->last_edit_author_id)->toEqual($user->id);
});

it('fails rows when related lookups are missing', function () {
    $user = User::factory()->create();
    auth()->login($user);

    $import = createImportForUser($user);
    $importer = $import->getImporter(defaultColumnMap(), []);

    $row = [
        'Barcode' => 'MISSING01',
        'CAS #' => '00-00-0',
        'WHMIS Hazard Classes' => 'Unknown',
        'Quantity' => '10',
        'Unit' => 'L',
        'Room' => 'B999',
        'Location' => 'Shelf X',
        'Last Edited By' => 'Tester',
    ];

    $existingCount = Container::count();

    expect(fn () => ($importer)($row))
        ->toThrow(RowImportFailedException::class, 'Unable to import container row: no chemical was found with CAS # [00-00-0].');

    expect(Container::where('barcode', 'MISSING01')->exists())->toBeFalse();
    expect(Container::count())->toBe($existingCount);
});
