<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WhmisHazardClassSeeder extends Seeder
{
    public function run(): void
    {
        $hazardClasses = [
            [
                'class_name' => 'Flammable Gases',
                'description' => 'Gases that can ignite (catch fire) at room temperature.',
                'symbol' => 'flame',
            ],
            [
                'class_name' => 'Flammable Liquids',
                'description' => 'Liquids that can ignite at room temperature or that give off flammable vapours.',
                'symbol' => 'flame',
            ],
            [
                'class_name' => 'Oxidizing Materials',
                'description' => 'Materials that can cause other materials to burn or support combustion.',
                'symbol' => 'flame-over-circle',
            ],
            [
                'class_name' => 'Gases Under Pressure',
                'description' => 'Gases stored in containers under pressure.',
                'symbol' => 'gas-cylinder',
            ],
            [
                'class_name' => 'Acute Toxicity',
                'description' => 'Materials that are toxic or fatal if inhaled, ingested, or absorbed through skin.',
                'symbol' => 'skull-crossbones',
            ],
            [
                'class_name' => 'Corrosive Materials',
                'description' => 'Materials that can burn skin or eyes, or corrode metals.',
                'symbol' => 'corrosion',
            ],
            [
                'class_name' => 'Health Hazard',
                'description' => 'Materials that may cause serious health effects.',
                'symbol' => 'health-hazard',
            ],
            [
                'class_name' => 'Environmental Hazard',
                'description' => 'Materials that may cause damage to the aquatic environment.',
                'symbol' => 'environment',
            ],
            [
                'class_name' => 'Non-Hazardous',
                'description' => 'Materials that do not meet any hazard classification criteria.',
                'symbol' => 'none',
            ],
        ];

        DB::table('whmis_hazard_classes')->insert($hazardClasses);
    }
}
