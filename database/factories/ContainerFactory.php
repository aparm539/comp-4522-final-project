<?php

namespace Database\Factories;

use App\Models\Chemical;
use App\Models\Container;
use App\Models\Location;
use App\Models\Shelf;
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
        $locationIDs = DB::table('locations')->pluck('id');
        $unitOfMeasureIDs = DB::table('unitsofmeasure')->pluck('id');
        $shelfIDs = DB::table('shelves')->pluck('id');
        return [
            'barcode' => $this->faker->unique()->bothify('MRUC******'),
            'quantity' => $this->faker->randomFloat(2, 0, 1000), // Random quantity between 0 and 1000
            'unit_of_measure_id' => $this->faker->randomElement($unitOfMeasureIDs),
            'location_id' => $this->faker->randomElement($locationIDs),
            'ishazardous' => $this->faker->boolean(),
            'supervisor_id' => $this->faker->randomElement($userIDs),
            'chemical_id' => random_int(1,30),
            'shelf_id' => $this->faker->randomElement($shelfIDs),
        ];
    }
}
