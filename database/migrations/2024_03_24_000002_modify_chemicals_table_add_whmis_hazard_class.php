<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chemicals', function (Blueprint $table) {
            $table->dropColumn('ishazardous');
            $table->foreignId('whmis_hazard_class_id')
                ->constrained('whmis_hazard_classes')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('chemicals', function (Blueprint $table) {
            $table->boolean('ishazardous')->default(true);
            $table->dropForeign(['whmis_hazard_class_id']);
            $table->dropColumn('whmis_hazard_class_id');
        });
    }
}; 