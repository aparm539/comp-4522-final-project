<?php

namespace Tests\Unit\Services;

use App\Models\Chemical;
use App\Models\Container;
use App\Models\Lab;
use App\Models\StorageCabinet;
use App\Models\UnitOfMeasure;
use App\Services\Container\ContainerService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ContainerServiceTest extends TestCase
{
    use RefreshDatabase;

    private ContainerService $containerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->containerService = app(ContainerService::class);
    }

    public function test_it_can_print_containers()
    {
        // Arrange
        /** @var Chemical $chemical */
        $chemical = Chemical::factory()->create();
        /** @var UnitOfMeasure $unitOfMeasure */
        $unitOfMeasure = UnitOfMeasure::factory()->create();
        /** @var Lab $lab */
        $lab = Lab::factory()->create();
        /** @var StorageCabinet $storageCabinet */
        $storageCabinet = StorageCabinet::factory()->create([
            'lab_id' => $lab->id,
        ]);

        $containers = Container::factory()->count(3)->create([
            'chemical_id' => $chemical->id,
            'unit_of_measure_id' => $unitOfMeasure->id,
            'lab_id' => $lab->id,
            'storage_cabinet_id' => $storageCabinet->id,
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
        $this->assertEquals('attachment; filename=containers.pdf', $response->headers->get('Content-Disposition'));
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_it_can_change_location_for_multiple_containers()
    {
        // Arrange
        $chemical = Chemical::factory()->create();
        $unitOfMeasure = UnitOfMeasure::factory()->create();
        $oldLab = Lab::factory()->create();
        $oldStorageCabinet = StorageCabinet::factory()->create([
            'lab_id' => $oldLab->id,
        ]);
        $newLab = Lab::factory()->create();
        $newStorageCabinet = StorageCabinet::factory()->create([
            'lab_id' => $newLab->id,
        ]);

        $containers = Container::factory()->count(3)->create([
            'chemical_id' => $chemical->id,
            'unit_of_measure_id' => $unitOfMeasure->id,
            'lab_id' => $oldLab->id,
            'storage_cabinet_id' => $oldStorageCabinet->id,
        ]);

        $data = [
            'lab_id' => $newLab->id,
            'storage_cabinet_id' => $newStorageCabinet->id,
        ];

        // Act
        $this->containerService->changeLab($containers, $data);

        // Assert
        $containers->each(function ($container) use ($newLab, $newStorageCabinet) {
            $this->assertEquals($newLab->id, $container->fresh()->lab_id);
            $this->assertEquals($newStorageCabinet->id, $container->fresh()->storage_cabinet_id);
        });
    }

    public function test_it_can_get_unavailable_labs()
    {
        // Arrange
        $lab = Lab::factory()->create();
        $lab->reconciliations()->create([
            'status' => 'ongoing',
        ]);
        $availableLab = Lab::factory()->create();

        // Act
        $unavailableLabs = $this->containerService->getUnavailableLabs();

        // Assert
        $this->assertCount(1, $unavailableLabs);
        $this->assertEquals($lab->id, $unavailableLabs->first()->id);
        $this->assertNotContains($availableLab->id, $unavailableLabs->pluck('id'));
    }

    public function test_it_can_change_lab_for_multiple_containers()
    {
        // Arrange
        $chemical = Chemical::factory()->create();
        $unitOfMeasure = UnitOfMeasure::factory()->create();
        $oldLab = Lab::factory()->create();
        $oldStorageCabinet = StorageCabinet::factory()->create([
            'lab_id' => $oldLab->id,
        ]);
        $newLab = Lab::factory()->create();
        $newStorageCabinet = StorageCabinet::factory()->create([
            'lab_id' => $newLab->id,
        ]);

        $containers = Container::factory()->count(3)->create([
            'chemical_id' => $chemical->id,
            'unit_of_measure_id' => $unitOfMeasure->id,
            'lab_id' => $oldLab->id,
            'storage_cabinet_id' => $oldStorageCabinet->id,
        ]);

        $data = [
            'lab_id' => $newLab->id,
            'storage_cabinet_id' => $newStorageCabinet->id,
        ];

        // Act
        $this->containerService->changeLab($containers, $data);

        // Assert
        $containers->each(function ($container) use ($newLab, $newStorageCabinet) {
            $this->assertEquals($newLab->id, $container->fresh()->lab_id);
            $this->assertEquals($newStorageCabinet->id, $container->fresh()->storage_cabinet_id);
        });
    }
}
