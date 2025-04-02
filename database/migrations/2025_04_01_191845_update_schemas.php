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
        Schema::table('containers', function (Blueprint $table) {
            $table->dropColumn('location_id');
            $table->dropColumn('ishazardous');
            $table->dropColumn('supervisor_id');
            $table->renameColumn('shelf_id','storage_cabinet_id');
        });

        Schema::table('chemicals', function (Blueprint $table) {
            $table->boolean('ishazardous');
        });

        Schema::table('reconciliations', function (Blueprint $table) {
            $table->renameColumn('shelf_id','storage_cabinet_id');
        });

        Schema::rename('shelves','storage_cabinets');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->renameColumn('storage_cabinet_id','shelf_id');
        });

        Schema::table('chemicals', function (Blueprint $table) {
            $table->dropColumn('ishazardous');
        });

        Schema::rename('storage_cabinets','shelves');

        Schema::table('containers', function (Blueprint $table) {
            $table -> integer('location_id');
            $table -> boolean('ishazardous');
            $table -> integer('supervisor_id');
            $table -> renameColumn('storage_cabinet_id','shelf_id');
        });
    }
};
