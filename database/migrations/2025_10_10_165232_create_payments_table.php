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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // ID interno
            $table->string('gateway'); // mercadopago, efipay, pagseguro
            $table->string('gateway_transaction_id')->nullable(); // ID do gateway
            $table->string('payment_method'); // pix, credit_card, bank_slip, etc.
            $table->morphs('payable'); // donations, orders, etc.
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('BRL');
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected', 'cancelled', 'refunded'])->default('pending');
            $table->string('payer_name')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('payer_phone')->nullable();
            $table->string('payer_document')->nullable();
            $table->text('pix_code')->nullable();
            $table->string('qr_code_url')->nullable();
            $table->string('checkout_url')->nullable();
            $table->json('gateway_response')->nullable(); // Resposta completa do gateway
            $table->json('metadata')->nullable(); // Dados extras
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('failure_reason')->nullable();
            $table->integer('installments')->default(1);
            $table->decimal('fee_amount', 8, 2)->nullable();
            $table->decimal('net_amount', 10, 2)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->index(['gateway', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('gateway_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
