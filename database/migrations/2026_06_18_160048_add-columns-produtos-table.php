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
        //
        Schema::table('produtos', function (Blueprint $table) {
        // Se preenchido, indica que este produto é "filho" de uma caixa
        $table->foreignId('produto_pai_id')->nullable()->constrained('produtos')->onDelete('set null');
        // Ex: Se for 4, significa que 1 caixa do pai gera 4 unidades deste produto
        $table->integer('fator_conversao')->default(1);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
