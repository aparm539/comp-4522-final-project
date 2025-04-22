<?php

namespace Database\Factories;

use App\Models\Chemical;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'ishazardous' => $this->faker->boolean(),
        ];
    }
}
