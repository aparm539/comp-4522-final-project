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
        Schema::create('reconciliations', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('ongoing'); // Status: 'ongoing', 'stopped', 'completed'
            $table->text('notes')->nullable(); // Optional remarks or notes about the reconciliation
            $table->timestamp('started_at')->nullable(); // Time when the reconciliation started
            $table->timestamp('ended_at')->nullable(); // Time when the reconciliation ended
            $table->foreignId('shelf_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('reconciliation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reconciliation_id')->constrained()->onDelete('cascade');
            $table->foreignId('container_id')->constrained()->onDelete('cascade');
            $table->integer('expected_quantity');
            $table->integer('actual_quantity')->nullable();
            $table->boolean('is_reconciled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliation_items');
        Schema::dropIfExists('reconciliation');
    }
};
