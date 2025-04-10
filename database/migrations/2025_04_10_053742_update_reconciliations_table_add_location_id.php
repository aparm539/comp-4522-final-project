<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add location_id as nullable
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
        });

        // Step 2: Update existing records
        DB::statement('
            UPDATE reconciliations r
            SET location_id = sc.location_id
            FROM storage_cabinets sc
            WHERE r.storage_cabinet_id = sc.id
        ');

        // Step 3: Make location_id required and remove storage_cabinet_id
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable(false)->change();
        });

        // Drop the storage_cabinet_id column and its foreign key if they exist
        if (Schema::hasColumn('reconciliations', 'storage_cabinet_id')) {
            Schema::table('reconciliations', function (Blueprint $table) {
                // Check if the foreign key exists in the database
                $foreignKeys = DB::select("
                    SELECT tc.constraint_name 
                    FROM information_schema.table_constraints tc 
                    WHERE tc.table_name = 'reconciliations' 
                    AND tc.constraint_type = 'FOREIGN KEY' 
                    AND tc.constraint_name = 'reconciliations_storage_cabinet_id_foreign'
                ");

                if (!empty($foreignKeys)) {
                    $table->dropForeign(['storage_cabinet_id']);
                }
                $table->dropColumn('storage_cabinet_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add storage_cabinet_id as nullable
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->foreignId('storage_cabinet_id')->nullable()->constrained()->onDelete('cascade');
        });

        // Step 2: Update existing records
        DB::statement('
            UPDATE reconciliations r
            SET storage_cabinet_id = sc.id
            FROM storage_cabinets sc
            WHERE sc.location_id = r.location_id
            AND sc.id = (
                SELECT id FROM storage_cabinets 
                WHERE location_id = r.location_id 
                LIMIT 1
            )
        ');

        // Step 3: Make storage_cabinet_id required and remove location_id
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->foreignId('storage_cabinet_id')->nullable(false)->change();
        });

        // Drop the location_id column and its foreign key if they exist
        if (Schema::hasColumn('reconciliations', 'location_id')) {
            Schema::table('reconciliations', function (Blueprint $table) {
                // Check if the foreign key exists in the database
                $foreignKeys = DB::select("
                    SELECT tc.constraint_name 
                    FROM information_schema.table_constraints tc 
                    WHERE tc.table_name = 'reconciliations' 
                    AND tc.constraint_type = 'FOREIGN KEY' 
                    AND tc.constraint_name = 'reconciliations_location_id_foreign'
                ");

                if (!empty($foreignKeys)) {
                    $table->dropForeign(['location_id']);
                }
                $table->dropColumn('location_id');
            });
        }
    }
};
