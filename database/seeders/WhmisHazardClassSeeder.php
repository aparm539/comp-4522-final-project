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
                'class_name' => 'Flammable',
                'description' => 'Gases that can ignite (catch fire) at room temperature.',
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
                'class_name' => 'Harmful / Irritant',
                'description' => 'Materials that are harmful or irritating.',
                'symbol' => 'exclamation-mark',
            ],
            [
                'class_name' => 'Explosive',
                'description' => 'Materials that may explode under certain conditions.',
                'symbol' => 'exploding-bomb',
            ],
            [
                'class_name' => 'Biohazardous',
                'description' => 'Materials that contain living organisms or toxins that can cause disease.',
                'symbol' => 'biohazardous',
            ],
        ];

        // Use updateOrInsert to prevent duplicates and allow updates if classes already exist
        // In one of the migrations I introduced duplicates, so hopefully this keeps it from breaking in the future 
        // if the db needs to be reseeded. 
        foreach ($hazardClasses as $class) {
            DB::table('whmis_hazard_classes')->updateOrInsert(
                ['class_name' => $class['class_name']],
                $class
            );
        }
    }
}
