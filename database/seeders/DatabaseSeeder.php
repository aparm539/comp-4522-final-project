<?php

namespace Database\Seeders;

use App\Models\Container;
use App\Models\StorageCabinet;
use App\Models\UnitOfMeasure;
use App\Models\User;
use App\Models\Location;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Unit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ChemicalSeeder::class,
            UnitOfMeasureSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
        ]);
        User::factory(3)->create();
        StorageCabinet::factory(5)
            ->recycle(
                Location::factory()
                    ->recycle(
                        User::factory()->create()
                    )
                    ->create()
            )
            ->create();
        Container::factory(10)
            ->recycle(
                StorageCabinet::factory()->create()
            )
            ->create();
    }
}
