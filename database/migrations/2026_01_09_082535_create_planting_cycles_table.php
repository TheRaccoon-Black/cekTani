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
        Schema::create('planting_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bed_id')->constrained();
            $table->foreignId('commodity_id')->constrained();
            $table->date('start_date');
            $table->date('estimated_harvest_date')->nullable();
            $table->integer('initial_plant_count');
            $table->integer('current_plant_count');
            $table->enum('status', ['active', 'harvested', 'failed', 'planned'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planting_cycles');
    }
};
