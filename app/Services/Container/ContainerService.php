<?php

namespace App\Services\Container;

use App\Models\Container;
use App\Models\Location;
use App\Services\Container\Exceptions\ContainerLocationUpdateException;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

readonly class ContainerService
{
    public function __construct(
        private Container $model,
        private ?LoggerInterface $logger,
        private PdfGeneratorServiceInterface $exporter,
    ) {}

    /**
     * Create a new container with logging.
     *
     * @throws Exception
     */
    public function create(array $data): Container
    {
        $this->logger?->info('Creating new container', ['data' => $data]);

        try {
            /** @var Container $container */
            $container = $this->model->create($data);

            $this->logger?->info('Container created successfully', ['id' => $container->id]);

            return $container;
        } catch (Exception $e) {
            $this->logger?->error('Failed to create container', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Find a container by ID.
     */
    public function find(int $id): ?Container
    {
        return $this->model->find($id);
    }

    /**
     * Change location for multiple containers.
     *
     * @throws ContainerLocationUpdateException
     */
    public function changeLocation(Collection $records, array $data): void
    {
        try {
            DB::beginTransaction();

            $this->logger?->info('Changing container locations', [
                'count' => $records->count(),
                'data' => $data,
            ]);

            $records->each(function ($record) use ($data) {
                $record->update([
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

        return $this->exporter->streamPdfDownload($containers);

    }

    public function getUnavailableLocations(): Collection
    {
        return Location::whereHas('reconciliations', function ($query) {
            $query->where('status', 'ongoing');
        })->get();
    }
}
