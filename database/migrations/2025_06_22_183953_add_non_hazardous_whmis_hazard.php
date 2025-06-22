<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('whmis_hazard_classes')->updateOrInsert(
            ['class_name' => 'Non-Hazardous'],
            [
                'class_name' => 'Non-Hazardous',
                'description' => 'Materials that do not meet any hazard classification criteria.',
                'symbol' => 'none',
            ]
        );

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('whmis_hazard_classes')
            ->whereIn('class_name', [
                'Non-Hazardous',
            ])
            ->delete();
    }
};
