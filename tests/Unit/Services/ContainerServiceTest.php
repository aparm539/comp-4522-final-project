<?php

namespace Tests\Unit\Services;

use App\Models\Chemical;
use App\Models\Container;
use App\Models\Location;
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
        /** @var Location $location */
        $location = Location::factory()->create();
        /** @var StorageCabinet $storageCabinet */
        $storageCabinet = StorageCabinet::factory()->create([
            'location_id' => $location->id,
        ]);

        $containers = Container::factory()->count(3)->create([
            'chemical_id' => $chemical->id,
            'unit_of_measure_id' => $unitOfMeasure->id,
            'location_id' => $location->id,
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
        $oldLocation = Location::factory()->create();
        $oldStorageCabinet = StorageCabinet::factory()->create([
            'location_id' => $oldLocation->id,
        ]);
        $newLocation = Location::factory()->create();
        $newStorageCabinet = StorageCabinet::factory()->create([
            'location_id' => $newLocation->id,
        ]);

        $containers = Container::factory()->count(3)->create([
            'chemical_id' => $chemical->id,
            'unit_of_measure_id' => $unitOfMeasure->id,
            'location_id' => $oldLocation->id,
            'storage_cabinet_id' => $oldStorageCabinet->id,
        ]);

        $data = [
            'location_id' => $newLocation->id,
            'storage_cabinet_id' => $newStorageCabinet->id,
        ];

        // Act
        $this->containerService->changeLocation($containers, $data);

        // Assert
        $containers->each(function ($container) use ($newLocation, $newStorageCabinet) {
            $this->assertEquals($newLocation->id, $container->fresh()->location_id);
            $this->assertEquals($newStorageCabinet->id, $container->fresh()->storage_cabinet_id);
        });
    }

    public function test_it_can_get_unavailable_locations()
    {
        // Arrange
        $location = Location::factory()->create();
        $location->reconciliations()->create([
            'status' => 'ongoing',
        ]);
        $availableLocation = Location::factory()->create();

        // Act
        $unavailableLocations = $this->containerService->getUnavailableLocations();

        // Assert
        $this->assertCount(1, $unavailableLocations);
        $this->assertEquals($location->id, $unavailableLocations->first()->id);
        $this->assertNotContains($availableLocation->id, $unavailableLocations->pluck('id'));
    }
}
