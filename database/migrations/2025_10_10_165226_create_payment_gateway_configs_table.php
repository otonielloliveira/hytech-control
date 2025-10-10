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
        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->unique(); // 'mercadopago', 'efipay', 'pagseguro'
            $table->string('name'); // Nome amigável
            $table->boolean('is_active')->default(false);
            $table->boolean('is_sandbox')->default(true);
            $table->json('credentials'); // Armazena chaves, tokens, etc.
            $table->json('settings')->nullable(); // Configurações específicas
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_configs');
    }
};
