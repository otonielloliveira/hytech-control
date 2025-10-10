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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do doador
            $table->string('email'); // Email do doador
            $table->string('phone')->nullable(); // Telefone do doador
            $table->decimal('amount', 10, 2); // Valor da doação
            $table->text('message')->nullable(); // Mensagem opcional
            $table->string('payment_method')->default('pix'); // Método de pagamento
            $table->string('payment_id')->nullable(); // ID do pagamento no gateway
            $table->string('pix_code')->nullable(); // Código PIX gerado
            $table->enum('status', ['pending', 'paid', 'cancelled', 'expired'])->default('pending');
            $table->timestamp('paid_at')->nullable(); // Data do pagamento
            $table->timestamp('expires_at')->nullable(); // Data de expiração
            $table->json('payment_data')->nullable(); // Dados adicionais do pagamento
            $table->string('ip_address')->nullable(); // IP do doador
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
