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
        DB::statement('ALTER TABLE containers DROP SYSTEM VERSIONING;');
        Schema::table('containers', function (Blueprint $table) {
            $table->foreignId('last_edit_author_id')
                ->constrained('users')
                ->nullable()
                ->onDelete('restrict');
        });
        DB::statement('ALTER TABLE containers ADD SYSTEM VERSIONING;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            //
        });
    }
};
