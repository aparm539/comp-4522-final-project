<?php

use App\Filament\Resources\StorageLocationResource\Pages\ListStorageLocations;
use App\Models\Lab;
use App\Models\Role;
use App\Models\StorageLocation;
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
it('storage location resource list page renders successfully', function () {
    $this->actingAs(getAdminUser());

    Livewire::test(ListStorageLocations::class)
        ->assertSuccessful();
});

/** @test */
it('an admin can create a storage location', function () {
    $this->actingAs(getAdminUser());

    /** @var Lab $lab */
    $lab = Lab::factory()->create();

    $data = [
        'name'    => 'Cabinet A',
        'barcode' => 'CAB123',
        'lab_id'  => $lab->id,
    ];

    Livewire::test(ListStorageLocations::class)
        ->mountTableAction('create')
        ->setTableActionData($data)
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors()
        ->assertCanSeeTableRecords(StorageLocation::query()->where('barcode', 'CAB123')->get());

    expect(StorageLocation::where('barcode', 'CAB123')->exists())->toBeTrue();
});

/** @test */
it('an admin can update a storage location', function () {
    $this->actingAs(getAdminUser());

    $lab = Lab::factory()->create();

    /** @var StorageLocation $storageLocation */
    $storageLocation = StorageLocation::factory()->create([
        'lab_id' => $lab->id,
    ]);

    Livewire::test(ListStorageLocations::class)
        ->mountTableAction('edit', $storageLocation)
        ->setTableActionData([
            'name' => 'Updated Cabinet',
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($storageLocation->refresh()->name)->toEqual('Updated Cabinet');
});

/** @test */
it('an admin can bulk delete storage locations', function () {
    $this->actingAs(getAdminUser());

    $lab = Lab::factory()->create();

    $locations = StorageLocation::factory()->count(3)->create([
        'lab_id' => $lab->id,
    ]);

    Livewire::test(ListStorageLocations::class)
        ->callTableBulkAction('delete', $locations);

    foreach ($locations as $location) {
        expect(StorageLocation::find($location->id))->toBeNull();
    }
}); 