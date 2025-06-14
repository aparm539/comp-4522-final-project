<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // if locations table exists
        if (Schema::hasTable('locations')) {
            Schema::rename('locations', 'labs');
                    // Rename location_id to lab_id in storage_cabinets
            Schema::table('storage_cabinets', function (Blueprint $table) {
                $table->renameColumn('location_id', 'lab_id');
            });
            // Rename location_id to lab_id in reconciliations
            Schema::table('reconciliations', function (Blueprint $table) {
                $table->renameColumn('location_id', 'lab_id');
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // if labs table exists
        if (Schema::hasTable('labs')) {
            Schema::rename('labs', 'locations');
        }

        // Rename lab_id back to location_id in storage_cabinets
        Schema::table('storage_cabinets', function (Blueprint $table) {
            $table->renameColumn('lab_id', 'location_id');
        });
        // Rename lab_id back to location_id in reconciliations
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->renameColumn('lab_id', 'location_id');
        });
    }
};
