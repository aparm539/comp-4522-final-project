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
        Schema::create('unitsofmeasure', function (Blueprint $table) {
            // Primary key
            $table->id(); // Equivalent to: $table->bigIncrements('id');

            // Fields
            $table->string('measure_name', 50);
            $table->string('abbreviation', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unitsofmeasure');
    }
};
