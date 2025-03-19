<?php

namespace Database\Factories;

use App\Models\Chemical;
use App\Models\Container;
use App\Models\Location;
use App\Models\Shelf;
use App\Models\User;
use App\Models\UnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'barcode' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'quantity' => $this->faker->randomFloat(2, 0, 1000), // Random quantity between 0 and 1000
            'unit_of_measure' => UnitOfMeasure::factory(),
            'location_id' => Location::factory(),
            'ishazardous' => $this->faker->boolean(),
            'date_added' => $this->faker->date(),
            'supervisor_id' => User::factory(),
            'chemical_id' => Chemical::factory(),
            'shelf_id' => Shelf::factory(),
        ];
    }
}
