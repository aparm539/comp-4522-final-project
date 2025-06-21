<?php

namespace Database\Factories;

use App\Models\Chemical;
use App\Models\WhmisHazardClass;
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
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Chemical $chemical): void {
            $hazardClass = WhmisHazardClass::inRandomOrder()->first();
            if ($hazardClass !== null) {
                $chemical->whmisHazardClasses()->attach($hazardClass->id);
            }
        });
    }
}
