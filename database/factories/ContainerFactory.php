<?php

namespace Database\Factories;

use App\Models\Container;
use App\Models\StorageCabinet;
use App\Models\User;
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
     */
    public function definition(): array
    {

        $unitOfMeasureIDs = DB::table('unitsofmeasure')->pluck('id');

        return [
            'barcode' => $this->faker->unique()->bothify('MRUC******'),
            'quantity' => $this->faker->randomFloat(2, 0, 1000), // Random quantity between 0 and 1000
            'unit_of_measure_id' => $this->faker->randomElement($unitOfMeasureIDs),
            'chemical_id' => random_int(1, 30),
            'storage_cabinet_id' => StorageCabinet::factory(),
            'last_edit_author_id' => User::factory(),
        ];
    }
}
