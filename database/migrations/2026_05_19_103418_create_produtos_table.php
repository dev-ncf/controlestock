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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained();
            $table->string('name');
            $table->string('sku')->unique(); // Código de barras ou SKU
            $table->decimal('purchase_price', 15, 2); // Preço de compra atual
            $table->decimal('sale_price', 15, 2);     // Preço de venda padrão
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock')->default(5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
