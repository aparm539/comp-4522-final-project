<?php

use App\Models\Chemical;
use App\Models\Container;
use App\Models\Lab;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use App\Services\Container\ContainerService;
use Barryvdh\DomPDF\Facade\Pdf;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->containerService = app(ContainerService::class);
});

test('it can print containers', function () {
    // Arrange
    /** @var Chemical $chemical */
    $chemical = Chemical::factory()->create();

    /** @var UnitOfMeasure $unitOfMeasure */
    $unitOfMeasure = UnitOfMeasure::factory()->create();

    /** @var Lab $lab */
    $lab = Lab::factory()->create();

    /** @var StorageLocation $storageLocation */
    $storageLocation = StorageLocation::factory()->create([
        'lab_id' => $lab->id,
    ]);

    $containers = Container::factory()->count(3)->create([
        'chemical_id' => $chemical->id,
        'unit_of_measure_id' => $unitOfMeasure->id,
        'storage_location_id' => $storageLocation->id,
    ]);

    $pdfMock = Mockery::mock('overload:'.Pdf::class);
    $pdfMock->shouldReceive('loadView')
        ->once()
        ->with('containers.print', ['containers' => $containers])
        ->andReturnSelf();
    $pdfMock->shouldReceive('stream')
        ->once()
        ->andReturn('pdf-content');

    // Act
    $response = $this->containerService->printContainers();

    // Assert
    expect($response->headers->get('Content-Disposition'))->toEqual('attachment; filename=containers.pdf');
    expect($response->headers->get('Content-Type'))->toEqual('application/pdf');
});

test('it can change location for multiple containers', function () {
    // Arrange
    $chemical = Chemical::factory()->create();
    $unitOfMeasure = UnitOfMeasure::factory()->create();
    $oldLab = Lab::factory()->create();
    $oldstorageLocation = StorageLocation::factory()->create([
        'lab_id' => $oldLab->id,
    ]);
    $newLab = Lab::factory()->create();
    $newstorageLocation = StorageLocation::factory()->create([
        'lab_id' => $newLab->id,
    ]);

    $containers = Container::factory()->count(3)->create([
        'chemical_id' => $chemical->id,
        'unit_of_measure_id' => $unitOfMeasure->id,
        'storage_location_id' => $oldstorageLocation->id,
    ]);

    $data = [
        'lab_id' => $newLab->id,
        'storage_location_id' => $newstorageLocation->id,
    ];

    // Act
    $this->containerService->changeLab($containers, $data);

    // Assert
    $containers->each(function ($container) use ($newLab, $newstorageLocation) {
        expect($container->fresh()->storageLocation->lab_id)->toEqual($newLab->id);
        expect($container->fresh()->storage_location_id)->toEqual($newstorageLocation->id);
    });
});

test('it can get unavailable labs', function () {
    // Arrange
    $lab = Lab::factory()->create();
    $lab->reconciliations()->create([
        'status' => 'ongoing',
    ]);
    $availableLab = Lab::factory()->create();

    // Act
    $unavailableLabs = $this->containerService->getUnavailableLabs();

    // Assert
    expect($unavailableLabs)->toHaveCount(1);
    expect($unavailableLabs->first()->id)->toEqual($lab->id);
    expect($unavailableLabs->pluck('id'))->not->toContain($availableLab->id);
});

test('it can change lab for multiple containers', function () {
    // Arrange
    $chemical = Chemical::factory()->create();
    $unitOfMeasure = UnitOfMeasure::factory()->create();
    $oldLab = Lab::factory()->create();
    $oldstorageLocation = StorageLocation::factory()->create([
        'lab_id' => $oldLab->id,
    ]);
    $newLab = Lab::factory()->create();
    $newstorageLocation = StorageLocation::factory()->create([
        'lab_id' => $newLab->id,
    ]);

    $containers = Container::factory()->count(3)->create([
        'chemical_id' => $chemical->id,
        'unit_of_measure_id' => $unitOfMeasure->id,
        'storage_location_id' => $oldstorageLocation->id,
    ]);

    $data = [
        'lab_id' => $newLab->id,
        'storage_location_id' => $newstorageLocation->id,
    ];

    // Act
    $this->containerService->changeLab($containers, $data);

    // Assert
    $containers->each(function ($container) use ($newLab, $newstorageLocation) {
        expect($container->fresh()->storageLocation->lab_id)->toEqual($newLab->id);
        expect($container->fresh()->storage_location_id)->toEqual($newstorageLocation->id);
    });
});