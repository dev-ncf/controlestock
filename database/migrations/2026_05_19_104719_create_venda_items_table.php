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
        Schema::create('venda_items', function (Blueprint $table) {
             $table->id();
            $table->foreignId('venda_id')->constrained()->onDelete('cascade');
            $table->foreignId('produto_id')->constrained();
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2); // Preço vendido
            $table->decimal('cost_price', 15, 2); // Preço de custo no momento da venda (Para lucro real)
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venda_items');
    }
};
