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
        Schema::table('unitsofmeasure', function (Blueprint $table) {
            $table->renameColumn('measure_name', 'name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unitsofmeasure', function (Blueprint $table) {
            $table->renameColumn('name', 'measure_name');
        });
    }
};
