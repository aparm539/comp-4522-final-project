<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\StorageCabinet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<StorageCabinet>
 */
class StorageCabinetFactory extends Factory
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
            'location_id' => Location::factory(),
        ];
    }
}
