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
        Schema::create('cycle_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planting_cycle_id')->constrained()->cascadeOnDelete();
            $table->date('log_date');
            $table->string('phase');
            $table->text('activity');
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycle_logs');
    }
};
