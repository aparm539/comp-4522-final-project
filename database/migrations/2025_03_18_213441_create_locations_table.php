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
        Schema::create('locations', function (Blueprint $table) {
            // Primary key
            $table->id(); // Equivalent to: $table->bigIncrements('id');

            // Fields
            $table->string('location_barcode', 15)->nullable();
            $table->json('location_json')->nullable();
            $table->unsignedBigInteger('supervisor_id')->nullable();

            // Foreign key constraints
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
