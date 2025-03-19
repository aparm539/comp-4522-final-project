<?php

namespace Database\Seeders;

use App\Models\Container;
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
        User::factory(10)->create();
        Container::factory(2000)->create();
        Location::factory(50)->create();
        UnitOfMeasure::factory(5)->create();
    }
}
