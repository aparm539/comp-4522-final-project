<?php

use App\Filament\Resources\ChemicalResource\Pages\ListChemicals;
use App\Models\Chemical;
use App\Models\Role;
use App\Models\User;
use App\Models\WhmisHazardClass;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

if (! function_exists('getAdminUser')) {
    function getAdminUser(): User
    {
        // Fetch the seeded admin user if it exists.
        /** @var User|null $user */
        $user = User::whereHas('roles', static fn ($q) => $q->where('name', 'admin'))->first();

        // Fallback in the unlikely event the seeder did not run.
        if (! $user) {
            /** @var User $user */
            $user = User::factory()->create();

            // Attach the already-seeded admin role (or create one if missing).
            $role = Role::firstOrCreate([
                'name' => 'admin',
            ], [
                'description' => 'Users with full administrative permissions',
            ]);

            $user->roles()->sync([$role->id]);
        }

        return $user;
    }
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

    // Use an existing WHMIS hazard class from the seeded data.
    $hazardClass = WhmisHazardClass::inRandomOrder()->first();

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

    /** @var Chemical $chemical */
    $chemical = Chemical::inRandomOrder()->first();

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

    $chemicals = Chemical::inRandomOrder()->limit(3)->get();

    Livewire::test(ListChemicals::class)
        ->callTableBulkAction('delete', $chemicals);

    foreach ($chemicals as $chemical) {
        expect(Chemical::find($chemical->id))->toBeNull();
    }
}); 