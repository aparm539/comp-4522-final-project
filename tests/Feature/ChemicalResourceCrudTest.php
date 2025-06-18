<?php

use App\Filament\Resources\ChemicalResource\Pages\ListChemicals;
use App\Models\Chemical;
use App\Models\Role;
use App\Models\User;
use App\Models\WhmisHazardClass;
use Livewire\Livewire;

/**
 * Helper function that returns a user with the "admin" role attached.
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

/** @test */
it('chemical resource list page renders successfully', function () {
    $this->actingAs(getAdminUser());

    Livewire::test(ListChemicals::class)
        ->assertSuccessful();
});

/** @test */
it('an admin can create a chemical', function () {
    $this->actingAs(getAdminUser());

    // We need at least one WHMIS hazard class to relate to.
    $hazardClass = WhmisHazardClass::create([
        'class_name'   => 'Flammable',
        'description'  => 'Flammable liquids',
        'symbol'       => 'flame',
    ]);

    $data = [
        'cas'                => '64-17-5',
        'name'               => 'Ethanol',
        'whmisHazardClasses' => [$hazardClass->id],
    ];

    Livewire::test(ListChemicals::class)
        ->mountTableAction('create')
        ->setTableActionData($data)
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors()
        ->call('gotoPage', 4)
        ->assertCanSeeTableRecords(Chemical::query()->where('name', 'Ethanol')->get());
    expect(Chemical::where('name', 'Ethanol')->exists())->toBeTrue();
});

/** @test */
it('an admin can update a chemical', function () {
    $this->actingAs(getAdminUser());

    $hazardClass = WhmisHazardClass::create([
        'class_name'   => 'Flammable',
        'description'  => 'Flammable liquids',
        'symbol'       => 'flame',
    ]);

    /** @var Chemical $chemical */
    $chemical = Chemical::factory()->create();

    $chemical->whmisHazardClasses()->attach($hazardClass->id);

    Livewire::test(ListChemicals::class)
        ->mountTableAction('edit', $chemical)
        ->setTableActionData([
            'name' => 'Updated Chemical',
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($chemical->refresh()->name)->toEqual('Updated Chemical');
});

/** @test */
it('an admin can bulk delete chemicals', function () {
    $this->actingAs(getAdminUser());

    $hazardClass = WhmisHazardClass::create([
        'class_name'   => 'Flammable',
        'description'  => 'Flammable liquids',
        'symbol'       => 'flame',
    ]);

    $chemicals = Chemical::factory()->count(3)->create();

    foreach ($chemicals as $chem) {
        $chem->whmisHazardClasses()->attach($hazardClass->id);
    }

    Livewire::test(ListChemicals::class)
        ->callTableBulkAction('delete', $chemicals);

    foreach ($chemicals as $chemical) {
        expect(Chemical::find($chemical->id))->toBeNull();
    }
}); 