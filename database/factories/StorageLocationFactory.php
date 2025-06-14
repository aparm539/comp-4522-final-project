<?php

namespace Database\Factories;

use App\Models\Lab;
use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<StorageLocation>
 */
class StorageLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'barcode' => $this->faker->unique()->bothify('MRUC******'),
            'name' => $this->faker->bothify('shelf #'),
            'lab_id' => Lab::factory(),
        ];
    }
}
