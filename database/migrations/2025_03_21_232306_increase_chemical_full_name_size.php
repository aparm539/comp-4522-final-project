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
        Schema::table('chemicals', function (Blueprint $table) {
            $table->string('full_name', 255)->change();
        });

        Schema::table('unitsofmeasure', function(Blueprint $table) {
            $table->string('abbreviation', 10)->change();
        });
        Schema::table('containers', function(Blueprint $table) {
            $table->date('created_at');
            $table->date('updated_at');
            $table->dropColumn('date_added');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chemicals', function (Blueprint $table) {
            $table->string('full_name', 100)->change();
        });
        Schema::table('unitsofmeasure', function(Blueprint $table) {
            $table->string('abbreviation', 3)->change();
        });
        Schema::table('containers', function(Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->date('date_added');
        });
        //
    }
};
