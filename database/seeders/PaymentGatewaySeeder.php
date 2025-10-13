<?php

namespace Database\Seeders;

use App\Models\PaymentGatewayConfig;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Configurando gateways de pagamento...');

        // 1. ASAAS - Gateway Principal (Ativo)
        $asaas = PaymentGatewayConfig::updateOrCreate(
            ['gateway' => 'asaas'],
            [
                'name' => 'ASAAS',
                'is_active' => true,
                'is_sandbox' => true,
                'credentials' => [
                    'api_key' => env('ASAAS_API_KEY'),
                ],
                'settings' => [
                    'webhook_url' => env('ASAAS_WEBHOOK_URL'),
                    'pix_enabled' => true,
                    'credit_card_enabled' => true,
                    'bank_slip_enabled' => true,
                    'max_installments' => 12,
                    'min_installment_amount' => 5.00,
                ],
                'description' => 'Gateway ASAAS - PIX, Cartão e Boleto',
                'sort_order' => 1,
            ]
        );

        // 2. MercadoPago - Gateway Secundário (Inativo por problemas de API)
        $mercadoPago = PaymentGatewayConfig::updateOrCreate(
            ['gateway' => 'mercadopago'],
            [
                'name' => 'MercadoPago',
                'is_active' => false, // Inativo devido aos problemas documentados
                'is_sandbox' => true,
                'credentials' => [
                    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
                    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
                ],
                'settings' => [
                    'webhook_url' => env('MERCADOPAGO_WEBHOOK_URL'),
                    'pix_enabled' => true,
                    'credit_card_enabled' => true,
                    'bank_slip_enabled' => false,
                ],
                'description' => 'Gateway MercadoPago - Inativo (problemas de API)',
                'sort_order' => 2,
            ]
        );

        // 3. EfiPay - Gateway Adicional (Inativo)
        $efiPay = PaymentGatewayConfig::updateOrCreate(
            ['gateway' => 'efipay'],
            [
                'name' => 'EFI Pay',
                'is_active' => false,
                'is_sandbox' => true,
                'credentials' => [
                    'client_id' => env('EFIPAY_CLIENT_ID'),
                    'client_secret' => env('EFIPAY_CLIENT_SECRET'),
                ],
                'settings' => [
                    'webhook_url' => env('EFIPAY_WEBHOOK_URL'),
                    'pix_enabled' => true,
                    'credit_card_enabled' => false,
                    'bank_slip_enabled' => true,
                ],
                'description' => 'Gateway EFI Pay - PIX e Boleto',
                'sort_order' => 3,
            ]
        );

        // 4. PagSeguro - Gateway Adicional (Inativo)
        $pagSeguro = PaymentGatewayConfig::updateOrCreate(
            ['gateway' => 'pagseguro'],
            [
                'name' => 'PagSeguro',
                'is_active' => false,
                'is_sandbox' => true,
                'credentials' => [
                    'email' => env('PAGSEGURO_EMAIL'),
                    'token' => env('PAGSEGURO_TOKEN'),
                ],
                'settings' => [
                    'webhook_url' => env('PAGSEGURO_WEBHOOK_URL'),
                    'pix_enabled' => false,
                    'credit_card_enabled' => true,
                    'bank_slip_enabled' => true,
                ],
                'description' => 'Gateway PagSeguro - Cartão e Boleto',
                'sort_order' => 4,
            ]
        );

        $this->command->info('✅ ASAAS configurado como gateway principal');
        $this->command->info('ℹ️  MercadoPago desativado (problemas de API)');
        
        // Mostrar status dos gateways
        $this->command->table(
            ['Gateway', 'Status', 'Ambiente', 'Métodos'],
            [
                ['ASAAS', '🟢 Ativo', 'Sandbox', 'PIX, Cartão, Boleto'],
                ['MercadoPago', '🔴 Inativo', 'Sandbox', 'PIX, Cartão'],
                ['EFI Pay', '🔴 Inativo', 'Sandbox', 'PIX, Boleto'],
                ['PagSeguro', '🔴 Inativo', 'Sandbox', 'Cartão, Boleto'],
            ]
        );
    }
}