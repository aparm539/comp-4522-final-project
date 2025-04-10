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
        Schema::create('chemicals', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('full_name', 100);
            $table->string('cas', 100);
        });
        Schema::table('containers', function (Blueprint $table) {
            $table->dropColumn('cas');
            $table->dropColumn('chemical_name');
            $table->dropColumn('container_name');
            $table->integer('chemical_id');
            $table->foreign('chemical_id')->references('id')->on('chemicals')->onDelete('set null');

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chemicals');
        Schema::table('containers', function (Blueprint $table) {
            $table->dropForeign('containers_chemical_id_foreign');
            $table->dropColumn('chemical_id');
            $table->string('cas', 100);
            $table->string('chemical_name', 100);
            $table->string('container_name', 100);
        });
    }
};
