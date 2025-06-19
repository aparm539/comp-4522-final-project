<?php

use App\Filament\Resources\ContainerResource\Pages\ListContainers;
use App\Models\Chemical;
use App\Models\Container;
use App\Models\Lab;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use App\Models\User;

use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);


// Define helper only if it hasn't been defined in another test file.
if (! function_exists('getAdminUser')) {
    /**
     * Returns a user with the "admin" role attached.
     */
    function getAdminUser(): User
    {   
        return User::whereHas('roles', static fn ($q) => $q->where('name', 'admin'))->first();
    }
}

/** @test */
it('an admin can create a container', function () {
    $this->actingAs(getAdminUser());

    // Use seeded data where possible.
    $chemical = Chemical::inRandomOrder()->first();
    $unit     = UnitOfMeasure::inRandomOrder()->first();

    /** @var StorageLocation $storageLocation */
    $storageLocation = StorageLocation::inRandomOrder()->first();

    /** @var Lab $lab */
    $lab = $storageLocation->lab;

    $data = [
        'chemical_id'         => $chemical->id,
        'unit_of_measure_id'  => $unit->id,
        'quantity'            => 10,
        'storage_location_id' => $storageLocation->id,
        'lab_id'              => $lab->id,
        'barcode'             => 'MRUC123456',
        'last_edit_author_id' => getAdminUser()->id,
    ];

    Livewire::test(ListContainers::class)
        ->mountTableAction('create')
        ->setTableActionData($data)
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors()
        ->call('gotoPage', 2)
        ->assertCanSeeTableRecords(Container::query()->where('barcode', 'MRUC123456')->get());
    expect(Container::where('barcode', 'MRUC123456')->exists())->toBeTrue();
});

/** @test */
it('an admin can update a container', function () {
    $this->actingAs(getAdminUser());

    /** @var Container $container */
    $container = Container::inRandomOrder()->first();

    $originalQuantity = $container->quantity;

    Livewire::test(ListContainers::class)
        ->mountTableAction('edit', $container)
        ->setTableActionData([
            'quantity' => $originalQuantity + 10,
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($container->refresh()->quantity)->toEqual($originalQuantity + 10);
});

/** @test */
it('an admin can bulk delete containers', function () {
    $this->actingAs(getAdminUser());

    $containers = Container::inRandomOrder()->limit(3)->get();

    Livewire::test(ListContainers::class)
        ->callTableBulkAction('delete', $containers);

    foreach ($containers as $container) {
        expect(Container::find($container->id))->toBeNull();
    }
}); 