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
        Schema::create('clientes', function (Blueprint $table) {
             $table->id();
            $table->string('name');
            $table->string('nif')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0); // Limite máximo de dívida
            $table->decimal('current_balance', 15, 2)->default(0); // O que ele deve atualmente
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
