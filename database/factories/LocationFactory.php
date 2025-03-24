<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

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
        $userIDs = DB::table('users')->pluck('id');
        return [
            'barcode' => $this->faker->unique()->bothify('MRUC******'),
            'room_number' => $this->faker->bothify('B2##'),
            'description' => $this->faker->sentence(),
            'supervisor_id' => $this->faker->randomElement($userIDs),
        ];
    }
}
