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
        // TODO: Fix this.
        Schema::table('labs', function (Blueprint $table) {
            $table->dropForeign(['chem_inventory_db.labs.locations_supervisor_id_foreign']);
            $table->dropColumn('supervisor_id');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('labs', function (Blueprint $table) {
            $table->foreignId('supervisor_id')
                ->constrained('users')
                ->onDelete('set null');
        });

    }
};
