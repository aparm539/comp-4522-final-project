<?php

namespace App\Services\Container;

use App\Models\Container;
use App\Models\Lab;
use App\Services\Container\Exceptions\ContainerLabUpdateException;
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
     * Change lab for multiple containers.
     *
     * @throws ContainerLabUpdateException
     */
    public function changeLab(Collection $records, array $data): void
    {
        try {
            DB::beginTransaction();

            $this->logger?->info('Changing container labs', [
                'count' => $records->count(),
                'data' => $data,
            ]);

            $records->each(function ($record) use ($data) {
                $record->update([
                    'storage_location_id' => $data['storage_location_id'],
                ]);
            });

            DB::commit();

            $this->logger?->info('Successfully changed container labs', [
                'count' => $records->count(),
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            $this->logger?->error('Failed to change container labs', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw new ContainerLabUpdateException(
                'Failed to update container labs: '.$e->getMessage(),
                $e
            );
        }
    }

    public function printContainers(): StreamedResponse
    {
        $containers = Container::all();

        return $this->exporter->streamPdfDownload($containers);

    }

    public function getUnavailableLabs(): Collection
    {
        return Lab::whereHas('reconciliations', function ($query) {
            $query->where('status', 'ongoing');
        })->get();
    }
}
