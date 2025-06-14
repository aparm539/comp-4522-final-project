<?php

namespace Database\Seeders;

use App\Models\Container;
use App\Models\Lab;
use App\Models\StorageCabinet;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            WhmisHazardClassSeeder::class,
            ChemicalSeeder::class,
            UnitOfMeasureSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);
        $users = User::factory(3)->create();
        $storageCabinets = StorageCabinet::factory(5)
            ->recycle(
                Lab::factory()
                    ->recycle(
                        $users->random()
                    )
                    ->create()
            )
            ->create();
        Container::factory(10)
            ->recycle(
                $storageCabinets,
                $users->random()
            )
            ->create();
    }
}
