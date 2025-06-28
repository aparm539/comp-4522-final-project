<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        DB::statement('ALTER TABLE containers DROP SYSTEM VERSIONING;');
        Schema::table('containers', function (Blueprint $table) {
            $table->string('barcode', 255)->change();
        });
        DB::statement('ALTER TABLE containers ADD SYSTEM VERSIONING;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
