<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'location_barcode' => $this->faker->unique()->regexify('[A-Z0-9]{15}'),
            'location_json' => [
                'shelf' => $this->faker->randomDigit(),
                'room_id' => $this->faker->randomDigit(),
                'description' => $this->faker->sentence(),
            ],
            'supervisor_id' => User::factory(),
        ];
    }
}
