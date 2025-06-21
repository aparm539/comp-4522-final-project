<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('whmis_hazard_classes')
            ->where('class_name', 'Flammable Gases')
            ->update([
                'class_name' => 'Flammable',
                'description' => 'Gases that can ignite (catch fire) at room temperature.',
                'symbol' => 'flame',
            ]);

        DB::table('whmis_hazard_classes')
            ->whereIn('class_name', [
                'Flammable Liquids',
                'Non-Hazardous',
            ])
            ->delete();

        $newClasses = [
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

        foreach ($newClasses as $class) {
            DB::table('whmis_hazard_classes')->updateOrInsert(
                ['class_name' => $class['class_name']],
                $class
            );
        }
    }

    public function down(): void
    {
        DB::table('whmis_hazard_classes')
            ->whereIn('class_name', [
                'Harmful / Irritant',
                'Explosive',
                'Biohazardous',
            ])
            ->delete();

        DB::table('whmis_hazard_classes')->updateOrInsert(
            ['class_name' => 'Flammable Liquids'],
            [
                'class_name' => 'Flammable Liquids',
                'description' => 'Liquids that can ignite at room temperature or that give off flammable vapours.',
                'symbol' => 'flame',
            ]
        );

        DB::table('whmis_hazard_classes')->updateOrInsert(
            ['class_name' => 'Non-Hazardous'],
            [
                'class_name' => 'Non-Hazardous',
                'description' => 'Materials that do not meet any hazard classification criteria.',
                'symbol' => 'none',
            ]
        );

        DB::table('whmis_hazard_classes')
            ->where('class_name', 'Flammable')
            ->update([
                'class_name' => 'Flammable Gases',
                'description' => 'Gases that can ignite (catch fire) at room temperature.',
                'symbol' => 'flame',
            ]);
    }
};
