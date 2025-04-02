<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StorageCabinet>
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
        $locationIDs = DB::table('locations')->pluck('id');
        return [
            'barcode' => $this->faker->unique()->bothify('MRUC******'),
            'name' => $this->faker->bothify('shelf #'),
            'location_id' => Location::factory(),
        ];
    }
}
