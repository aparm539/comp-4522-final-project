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
        if (Schema::hasColumn('locations', 'location_json')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('location_json');
                $table->string('shelf')->nullable();
                $table->string('room_number')->nullable();
                $table->string('description')->nullable();
            });
        };

        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->json('location_json')->nullable();
            $table->dropColumn(['shelf','room_number', 'description']);
        });
        //
    }
};
