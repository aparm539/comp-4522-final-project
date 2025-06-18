<?php

use App\Filament\Resources\LabResource\Pages\ListLabs;
use App\Models\Lab;
use App\Models\Role;
use App\Models\User;
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
it('lab resource list page renders successfully', function () {
    $this->actingAs(getAdminUser());

    Livewire::test(ListLabs::class)
        ->assertSuccessful();
});

/** @test */
it('an admin can create a lab', function () {
    $this->actingAs(getAdminUser());

    $data = [
        'room_number' => 'B201',
        'description' => 'Organic Chemistry Lab',
    ];

    Livewire::test(ListLabs::class)
        ->mountTableAction('create')
        ->setTableActionData($data)
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors()
        ->assertCanSeeTableRecords(Lab::query()->where('room_number', 'B201')->get());

    expect(Lab::where('room_number', 'B201')->exists())->toBeTrue();
});

/** @test */
it('an admin can update a lab', function () {
    $this->actingAs(getAdminUser());

    /** @var Lab $lab */
    $lab = Lab::factory()->create();

    Livewire::test(ListLabs::class)
        ->mountTableAction('edit', $lab)
        ->setTableActionData([
            'description' => 'Updated Description',
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($lab->refresh()->description)->toEqual('Updated Description');
});

/** @test */
it('an admin can bulk delete labs', function () {
    $this->actingAs(getAdminUser());

    $labs = Lab::factory()->count(3)->create();

    Livewire::test(ListLabs::class)
        ->callTableBulkAction('delete', $labs);

    foreach ($labs as $lab) {
        expect(Lab::find($lab->id))->toBeNull();
    }
}); 