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
        Schema::rename('storage_cabinets', 'storage_locations');
        Schema::table('containers', function (Blueprint $table) {
            $table->renameColumn('storage_cabinet_id', 'storage_location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('storage_locations', 'storage_cabinets');
        Schema::table('containers', function (Blueprint $table) {
            $table->renameColumn('storage_location_id', 'storage_cabinet_id');
        });
    }
};
