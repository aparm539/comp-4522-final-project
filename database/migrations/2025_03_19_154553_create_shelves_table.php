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
        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('barcode');
            $table->string('name', 50);
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });

        Schema::table('containers', function (Blueprint $table) {
            $table->integer('shelf_id');
            $table->foreign('shelf_id')->references('id')->on('shelves')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            $table->dropForeign('containers_shelf_id_foreign');
            $table->dropColumn('shelf_id');
        });
        Schema::dropIfExists('shelves');

    }
};
