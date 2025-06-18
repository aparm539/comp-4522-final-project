<?php

use App\Filament\Resources\ContainerResource\Pages\ListContainers;
use App\Models\Chemical;
use App\Models\Container;
use App\Models\Lab;
use App\Models\Role;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use App\Models\User;
use App\Models\WhmisHazardClass;
use Illuminate\Support\Str;
use Livewire\Livewire;

// Define helper only if it hasn't been defined in another test file.
if (! function_exists('getAdminUser')) {
    /**
     * Returns a user with the "admin" role attached.
     */
    function getAdminUser(): User
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Ensure an "admin" role exists and attach it to the user.
        $role = Role::firstOrCreate([
            'name' => 'admin',
        ], [
            'description' => 'Users with full administrative permissions',
        ]);

        $user->roles()->sync([$role->id]);

        return $user;
    }
}

/** @test */
it('an admin can create a container', function () {
    $this->actingAs(getAdminUser());

    // Create prerequisite data.
    $hazardClass = WhmisHazardClass::create([
        'class_name'  => 'Flammable',
        'description' => 'Flammable liquids',
        'symbol'      => 'flame',
    ]);

    $chemical = Chemical::factory()->create();
    $chemical->whmisHazardClasses()->attach($hazardClass->id);

    /** @var UnitOfMeasure $unit */
    $unit = UnitOfMeasure::factory()->create();

    /** @var Lab $lab */
    $lab = Lab::factory()->create();

    /** @var StorageLocation $storageLocation */
    $storageLocation = StorageLocation::factory()->create([
        'lab_id' => $lab->id,
    ]);

    $data = [
        'chemical_id'         => $chemical->id,
        'unit_of_measure_id'  => $unit->id,
        'quantity'            => 10,
        'storage_location_id' => $storageLocation->id,
        'lab_id'              => $lab->id,
        'barcode'             => 'MRUC123456',
        'last_edit_author_id' => auth()?->user()?->id,
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

    // Prerequisites
    $hazardClass = WhmisHazardClass::create([
        'class_name'  => 'Flammable',
        'description' => 'Flammable liquids',
        'symbol'      => 'flame',
    ]);

    $chemical = Chemical::factory()->create();
    $chemical->whmisHazardClasses()->attach($hazardClass->id);

    $unit  = UnitOfMeasure::factory()->create();
    $lab   = Lab::factory()->create();
    $storageLocation = StorageLocation::factory()->create([
        'lab_id' => $lab->id,
    ]);

    /** @var Container $container */
    $container = Container::factory()->create([
        'chemical_id'         => $chemical->id,
        'unit_of_measure_id'  => $unit->id,
        'quantity'            => 5,
        'storage_location_id' => $storageLocation->id,
        'barcode'             => 'MRUC123456',
    ]);

    Livewire::test(ListContainers::class)
        ->mountTableAction('edit', $container)
        ->setTableActionData([
            'quantity' => 15,
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($container->refresh()->quantity)->toEqual(15.0);
});

/** @test */
it('an admin can bulk delete containers', function () {
    $this->actingAs(getAdminUser());

    // Prerequisites
    $hazardClass = WhmisHazardClass::create([
        'class_name'  => 'Flammable',
        'description' => 'Flammable liquids',
        'symbol'      => 'flame',
    ]);

    $chemical = Chemical::factory()->create();
    $chemical->whmisHazardClasses()->attach($hazardClass->id);

    $unit  = UnitOfMeasure::factory()->create();
    $lab   = Lab::factory()->create();
    $storageLocation = StorageLocation::factory()->create([
        'lab_id' => $lab->id,
    ]);

    $containers = Container::factory()->count(3)->create([
        'chemical_id'         => $chemical->id,
        'unit_of_measure_id'  => $unit->id,
        'storage_location_id' => $storageLocation->id,
    ]);

    Livewire::test(ListContainers::class)
        ->callTableBulkAction('delete', $containers);

    foreach ($containers as $container) {
        expect(Container::find($container->id))->toBeNull();
    }
}); 