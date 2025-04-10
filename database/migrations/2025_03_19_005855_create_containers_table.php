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
        Schema::create('containers', function (Blueprint $table) {
            // Primary key
            $table->id(); // Equivalent to: $table->bigIncrements('id');

            // Fields
            $table->string('barcode', 10)->nullable();
            $table->string('cas', 50);
            $table->double('quantity');
            $table->unsignedBigInteger('unit_of_measure');
            $table->unsignedBigInteger('location_id');
            $table->boolean('ishazardous')->nullable();
            $table->date('date_added');
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->string('chemical_name', 255)->nullable();
            $table->string('container_name', 255)->nullable();

            // Foreign key constraints
            $table->foreign('unit_of_measure')->references('id')->on('unitsofmeasure')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};

