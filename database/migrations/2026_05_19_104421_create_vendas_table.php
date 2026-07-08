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
        Schema::create('vendas', function (Blueprint $table) {
           $table->id();
            $table->foreignId('cliente_id')->constrained();
            $table->string('invoice_number')->unique(); // Ex: FT 2024/001
            $table->dateTime('date');
            $table->dateTime('due_date'); // Vencimento da dívida
            $table->decimal('total_amount', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'unpaid', 'partial', 'paid', 'canceled'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
