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
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->dropforeign('reconciliations_storage_cabinet_id_foreign');
            $table->dropColumn('storage_cabinet_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add storage_cabinet_id as nullable
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->foreignId('storage_cabinet_id')->constrained()->onDelete('cascade');
        });

        // Drop the location_id column and its foreign key if they exist
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
        });
    }
};
