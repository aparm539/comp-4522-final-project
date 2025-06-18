<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('storage_locations', function (Blueprint $table): void {
            // Make the `barcode` column nullable
            $table->string('barcode')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Replace null barcodes with the string "null" so the NOT NULL constraint can be re-applied safely
        DB::table('storage_locations')
            ->whereNull('barcode')
            ->update(['barcode' => 'null']);

        Schema::table('storage_locations', function (Blueprint $table): void {
            // Revert the `barcode` column to NOT NULL
            $table->string('barcode')->nullable(false)->change();
        });
    }
}; 