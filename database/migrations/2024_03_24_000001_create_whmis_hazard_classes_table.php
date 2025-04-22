<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whmis_hazard_classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_name');
            $table->text('description');
            $table->string('symbol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whmis_hazard_classes');
    }
}; 