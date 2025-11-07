<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mapping: duplicate_id => original_id
        $duplicateMapping = [
            12 => 1, // Harmful / Irritant
            13 => 2, // Explosive
            14 => 3, // Biohazardous
        ];

        // Step 1: Migrate pivot table relationships
        // Handle each duplicate ID mapping
        foreach ($duplicateMapping as $duplicateId => $originalId) {
            // Check if duplicate ID exists in the hazard classes table
            $duplicateExists = DB::table('whmis_hazard_classes')
                ->where('id', $duplicateId)
                ->exists();

            if (!$duplicateExists) {
                // Skip if duplicate doesn't exist (idempotent)
                continue;
            }

            // Get all pivot table entries using the duplicate ID
            $pivotEntries = DB::table('chemical_whmis_hazard_class')
                ->where('whmis_hazard_class_id', $duplicateId)
                ->get();

            foreach ($pivotEntries as $entry) {
                $chemicalId = $entry->chemical_id;

                // Check if this chemical already has a relationship with the original ID
                $existingRelationship = DB::table('chemical_whmis_hazard_class')
                    ->where('chemical_id', $chemicalId)
                    ->where('whmis_hazard_class_id', $originalId)
                    ->exists();

                if ($existingRelationship) {
                    // If original relationship exists, just delete the duplicate relationship
                    DB::table('chemical_whmis_hazard_class')
                        ->where('chemical_id', $chemicalId)
                        ->where('whmis_hazard_class_id', $duplicateId)
                        ->delete();
                } else {
                    // Update the relationship to point to the original ID
                    DB::table('chemical_whmis_hazard_class')
                        ->where('chemical_id', $chemicalId)
                        ->where('whmis_hazard_class_id', $duplicateId)
                        ->update(['whmis_hazard_class_id' => $originalId]);
                }
            }
        }

        // Step 2: Delete duplicate records from whmis_hazard_classes table
        // Only delete if they still exist
        DB::table('whmis_hazard_classes')
            ->whereIn('id', array_keys($duplicateMapping))
            ->delete();

        // Step 3: Add unique constraint on class_name to prevent future duplicates
        Schema::table('whmis_hazard_classes', function (Blueprint $table): void {
            $table->unique('class_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove unique constraint
        Schema::table('whmis_hazard_classes', function (Blueprint $table): void {
            $table->dropUnique(['class_name']);
        });

        // This does not restore the deleted duplicate records.
    }
};
