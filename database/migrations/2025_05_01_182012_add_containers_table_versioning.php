<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
