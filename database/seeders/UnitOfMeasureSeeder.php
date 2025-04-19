<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitOfMeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitsOfMeasure = [
            ['name' => 'Milliliter', 'abbreviation' => 'mL'],
            ['name' => 'Liter', 'abbreviation' => 'L'],
            ['name' => 'Fluid Ounce', 'abbreviation' => 'fl oz'],
            ['name' => 'Gallon', 'abbreviation' => 'Gal'],
            ['name' => 'Microliter', 'abbreviation' => 'μL'],
            ['name' => 'Milligram', 'abbreviation' => 'mg'],
            ['name' => 'Gram', 'abbreviation' => 'g'],
            ['name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['name' => 'Pound', 'abbreviation' => 'lb'],
        ];

        DB::table('unitsofmeasure')->insert($unitsOfMeasure);
    }
}
