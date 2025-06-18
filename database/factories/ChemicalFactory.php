<?php

namespace Database\Factories;

use App\Models\Chemical;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\WhmisHazardClass;

/**
 * @extends Factory<Chemical>
 */
class ChemicalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cas' => $this->faker->bothify('??-###-##'),
            'name' => $this->faker->word(),
            'whmis_hazard_class_id' => WhmisHazardClass::inRandomOrder()->first()->id,
        ];
    }
}
