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
    Schema::create('shopping_items', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('url')->nullable();
        $table->decimal('estimated_price', 15, 2)->default(0);
        $table->decimal('quantity', 10, 2)->default(1);
        $table->string('unit')->default('pcs'); 

        $table->enum('type', ['direct', 'stock'])->default('stock');

        $table->foreignId('inventory_id')->nullable()->constrained('inventories')->nullOnDelete();

        $table->boolean('is_purchased')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_items');
    }
};
