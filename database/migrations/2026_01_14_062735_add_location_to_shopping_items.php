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
    Schema::table('shopping_items', function (Blueprint $table) {
        $table->foreignId('land_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('sector_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopping_items', function (Blueprint $table) {
            //
        });
    }
};
