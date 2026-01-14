<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('schedules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('land_id')->constrained()->onDelete('cascade'); 
        $table->foreignId('sector_id')->nullable()->constrained()->onDelete('cascade');
        $table->foreignId('bed_id')->nullable()->constrained()->onDelete('cascade');
        $table->foreignId('planting_cycle_id')->nullable()->constrained()->onDelete('cascade');

        $table->string('title');
        $table->date('due_date');
        $table->string('type')->default('general');
        $table->enum('status', ['pending', 'completed', 'missed'])->default('pending');
        $table->text('notes')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
