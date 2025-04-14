<?php

namespace App\Services\Container;

use App\Models\Container;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class ContainerService
{
    public function __construct(
        private Container $model,
        private ?LoggerInterface $logger = null,
        private ?ContainerAuthorizationInterface $authorization = null,
        private ?ContainerExportInterface $exporter = null
    ) {}

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
    public function getContainersWithRelations(): Collection
    {
        return $this->model
            ->with(['chemical', 'location', 'storageCabinet', 'unitOfMeasure'])
            ->get();
    }

    /**
     * Get containers for reconciliation with specific conditions
     */
    public function getContainersForReconciliation(Location $location, User $user): Collection
    {
        return $this->model
            ->whereHas('storageCabinet', function ($query) use ($location) {
                $query->where('location_id', $location->id);
            })
            ->whereDoesntHave('reconciliationItems', function ($query) {
                $query->where('status', 'pending');
            })
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                $query->whereHas('storageCabinet.location', function ($query) use ($user) {
                    $query->where('supervisor_id', $user->id);
                });
            })
            ->with(['chemical', 'unitOfMeasure'])
            ->get();
    }

    /**
     * Change location for multiple containers
     *
     * @throws ContainerLocationUpdateException
     */
    public function changeLocation(Collection $records, array $data): void
    {
        try {
            DB::beginTransaction();
            
            $this->logger?->info('Changing container locations', [
                'count' => $records->count(),
                'data' => $data
            ]);

            $records->each(function ($record) use ($data) {
                $record->update([
                    'location_id' => $data['location_id'],
                    'storage_cabinet_id' => $data['storage_cabinet_id']
                ]);
            });

            DB::commit();

            $this->logger?->info('Successfully changed container locations', [
                'count' => $records->count()
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            
            $this->logger?->error('Failed to change container locations', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            throw new ContainerLocationUpdateException(
                "Failed to update container locations: " . $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Export containers based on user permissions
     *
     * @throws AuthorizationException
     */
    public function exportContainers(User $user): Response
    {
        if (!$this->authorization?->canExport($user)) {
            throw new AuthorizationException('User cannot export containers');
        }

        $containers = $this->model
            ->with(['chemical', 'location'])
            ->where('is_active', true)
            ->get();

        return $this->exporter?->export($containers) 
            ?? response()->json($containers);
    }

    /**
     * Create a new container with logging
     *
     * @throws Exception
     */
    public function create(array $data): Container
    {
        $this->logger?->info('Creating new container', ['data' => $data]);

        try {
            $container = $this->model->create($data);
            
            $this->logger?->info('Container created successfully', [
                'id' => $container->id
            ]);

            return $container;
        } catch (Exception $e) {
            $this->logger?->error('Failed to create container', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            throw $e;
        }
    }
} 