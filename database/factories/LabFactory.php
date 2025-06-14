<?php

namespace Database\Factories;

use App\Models\Lab;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LabFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lab::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'room_number' => $this->faker->bothify('B2##'),
            'description' => $this->faker->sentence(),

        ];
    }
}
