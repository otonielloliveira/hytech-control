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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('gateway', ['asaas', 'mercadopago', 'pagseguro', 'pix', 'boleto', 'card']); 
            $table->json('config')->nullable(); // Configurações específicas do gateway
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->decimal('fee_percentage', 5, 2)->default(0); // Taxa percentual
            $table->decimal('fee_fixed', 8, 2)->default(0); // Taxa fixa
            $table->json('supported_currencies')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
