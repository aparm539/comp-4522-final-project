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
            $table->dropForeign('containers_location_id_foreign');
            $table->dropForeign('containers_supervisor_id_foreign');
            $table->dropForeign('containers_shelf_id_foreign');
            $table->dropColumn('location_id');
            $table->dropColumn('ishazardous');
            $table->dropColumn('supervisor_id');
            $table->dropColumn('shelf_id');
            $table->unsignedBigInteger('storage_cabinet_id');
        });

        Schema::table('chemicals', function (Blueprint $table) {
            $table->boolean('ishazardous');
        });

        Schema::table('reconciliations', function (Blueprint $table) {
            $table->dropForeign('reconciliations_shelf_id_foreign');
            $table->dropColumn('shelf_id');
            $table->unsignedBigInteger('storage_cabinet_id');
        });

        Schema::rename('shelves', 'storage_cabinets');

        Schema::table('containers', function (Blueprint $table) {
            $table->foreign('storage_cabinet_id')->references('id')->on('storage_cabinets');
        });
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->foreign('storage_cabinet_id')->references('id')->on('storage_cabinets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->renameColumn('storage_cabinet_id', 'shelf_id');
        });

        Schema::table('chemicals', function (Blueprint $table) {
            $table->dropColumn('ishazardous');
        });

        Schema::rename('storage_cabinets', 'shelves');

        Schema::table('containers', function (Blueprint $table) {
            $table->integer('location_id');
            $table->boolean('ishazardous');
            $table->integer('supervisor_id');
            $table->renameColumn('storage_cabinet_id', 'shelf_id');
        });
    }
};
