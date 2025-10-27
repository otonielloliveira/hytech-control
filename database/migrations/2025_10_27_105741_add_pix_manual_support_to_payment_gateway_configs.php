<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_gateway_configs', function (Blueprint $table) {
            // A tabela já tem os campos necessários (credentials e settings como JSON)
            // Apenas adicionar um comentário para documentação
        });
        
        // Criar um gateway PIX Manual padrão se não existir
        if (!DB::table('payment_gateway_configs')->where('gateway', 'pix_manual')->exists()) {
            DB::table('payment_gateway_configs')->insert([
                'name' => 'PIX Manual',
                'gateway' => 'pix_manual',
                'is_active' => false,
                'is_sandbox' => false,
                'sort_order' => 1,
                'description' => 'Pagamento PIX utilizando sua própria chave PIX',
                'credentials' => json_encode([]),
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_configs', function (Blueprint $table) {
            // Remover o gateway PIX Manual
            DB::table('payment_gateway_configs')->where('gateway', 'pix_manual')->delete();
        });
    }
};
