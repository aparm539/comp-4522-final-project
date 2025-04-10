<?php

namespace Database\Factories;

use App\Models\Chemical;
use App\Models\Container;
use App\Models\Location;
use App\Models\StorageCabinet;
use App\Models\User;
use App\Models\UnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class ContainerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Container::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $userIDs = DB::table('users')->pluck('id');
        $unitOfMeasureIDs = DB::table('unitsofmeasure')->pluck('id');
        $shelfIDs = DB::table('storage_cabinets')->pluck('id');


        return [
            'barcode' => $this->faker->unique()->bothify('MRUC******'),
            'quantity' => $this->faker->randomFloat(2, 0, 1000), // Random quantity between 0 and 1000
            'unit_of_measure_id' => $this->faker->randomElement($unitOfMeasureIDs),
            'chemical_id' => random_int(1,30),
            'storage_cabinet_id' => StorageCabinet::factory(),
        ];
    }
}
