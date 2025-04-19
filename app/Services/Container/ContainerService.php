<?php

namespace App\Services\Container;

use App\Models\Container;
use App\Models\Location;
use App\Models\User;
use App\Services\Container\Exceptions\ContainerLocationUpdateException;
use Exception;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContainerService
{
    public function __construct(
        private Container $model,
        private ?LoggerInterface $logger,
        private PdfGeneratorServiceInterface $exporter,
    ) {}

    /**
     * Create a new container with logging
     *
     * @throws Exception
     */
    public function create(array $data): Container
    {
        if ($this->logger) {
            $this->logger->info('Creating new container', ['data' => $data]);
        }

        try {
            $container = $this->model->create($data);

            if ($this->logger) {
                $this->logger->info('Container created successfully', ['id' => $container->id]);
            }

            return $container;
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->error('Failed to create container', [
                    'error' => $e->getMessage(),
                    'data' => $data,
                ]);
            }
            throw $e;
        }
    }

    /**
     * Find a container by ID
     */
    public function find(int $id): ?Container
    {
        return $this->model->find($id);
    }

    /**
     * Get containers with all related data
     */
    public function findWithRelations(int $id): ?Container
    {
        return $this->model
            ->with(['chemical', 'location', 'storageCabinet', 'unitOfMeasure'])
            ->find($id);
    }

    /**
     * Change location for multiple containers
     *
     * @throws ContainerLocationUpdateException
     */
    public function changeLocation(\Illuminate\Database\Eloquent\Collection $records, array $data): void
    {
        try {
            DB::beginTransaction();

            $this->logger?->info('Changing container locations', [
                'count' => $records->count(),
                'data' => $data,
            ]);

            $records->each(function ($record) use ($data) {
                $record->update([
                    'location_id' => $data['location_id'],
                    'storage_cabinet_id' => $data['storage_cabinet_id'],
                ]);
            });

            DB::commit();

            $this->logger?->info('Successfully changed container locations', [
                'count' => $records->count(),
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            $this->logger?->error('Failed to change container locations', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw new ContainerLocationUpdateException(
                'Failed to update container locations: '.$e->getMessage(),
                $e
            );
        }
    }

    public function printContainers(): StreamedResponse
    {
        $containers = Container::all();
        $response = $this->exporter->streamPdfDownload($containers);

        return $response;
    }

    public function getUnavailableLocations()
    {
        return Location::whereHas('reconciliations', function ($query) {
            $query->where('status', 'ongoing');
        })->get();
    }

    /**
     * Get containers for reconciliation with specific conditions
     */
    public function getContainersForReconciliation(Location $location, User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model
            ->whereHas('storageCabinet', function ($query) use ($location) {
                $query->where('location_id', $location->id);
            })
            ->whereDoesntHave('reconciliationItems', function ($query) {
                $query->where('status', 'pending');
            })
            ->when(! $user->isAdmin(), function ($query) use ($user) {
                $query->whereHas('storageCabinet.location', function ($query) use ($user) {
                    $query->where('supervisor_id', $user->id);
                });
            })
            ->with(['chemical', 'unitOfMeasure'])
            ->get();
    }
}
