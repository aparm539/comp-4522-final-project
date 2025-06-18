<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create pivot table
        Schema::create('chemical_whmis_hazard_class', function (Blueprint $table): void {
            $table->foreignId('chemical_id')->constrained()->cascadeOnDelete();
            $table->foreignId('whmis_hazard_class_id')->constrained()->cascadeOnDelete();

            // Prevent duplicate assignments of the same hazard class to the same chemical
            // Use a short index name to stay within the 64-character limit of MySQL / MariaDB identifiers.
            $table->unique(['chemical_id', 'whmis_hazard_class_id'], 'chem_whmis_unique');
        });

        // Migrate existing data from chemicals.whmis_hazard_class_id to the new pivot if the column still exists
        if (Schema::hasColumn('chemicals', 'whmis_hazard_class_id')) {
            DB::table('chemicals')
                ->whereNotNull('whmis_hazard_class_id')
                ->orderBy('id')
                ->chunkById(100, function ($chemicals): void {
                    $rows = [];
                    foreach ($chemicals as $chemical) {
                        $rows[] = [
                            'chemical_id'          => $chemical->id,
                            'whmis_hazard_class_id' => $chemical->whmis_hazard_class_id,
                        ];
                    }
                    if ($rows !== []) {
                        DB::table('chemical_whmis_hazard_class')->insertOrIgnore($rows);
                    }
                });

            // Remove the legacy foreign key and column
            Schema::table('chemicals', function (Blueprint $table): void {
                $table->dropForeign(['whmis_hazard_class_id']);
                $table->dropColumn('whmis_hazard_class_id');
            });
        }
    }

    public function down(): void
    {
        // Re-add the column on chemicals (nullable to avoid data-loss if multiple classes were assigned)
        Schema::table('chemicals', function (Blueprint $table): void {
            $table->foreignId('whmis_hazard_class_id')
                ->nullable()
                ->after('name')
                ->constrained()
                ->cascadeOnDelete();
        });

        // Attempt to restore data from the pivot (only the first hazard class per chemical will be copied back)
        DB::table('chemical_whmis_hazard_class')
            ->select('chemical_id', 'whmis_hazard_class_id')
            ->orderBy('chemical_id')
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    DB::table('chemicals')
                        ->where('id', $item->chemical_id)
                        ->update(['whmis_hazard_class_id' => $item->whmis_hazard_class_id]);
                }
            });

        Schema::dropIfExists('chemical_whmis_hazard_class');
    }
}; 