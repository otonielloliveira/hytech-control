<?php

namespace Database\Seeders;

use App\Models\PaymentGatewayConfig;
use Illuminate\Database\Seeder;

class PaymentGatewayConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            [
                'gateway' => 'mercadopago',
                'name' => 'MercadoPago',
                'is_active' => false,
                'is_sandbox' => true,
                'credentials' => [
                    'access_token' => '',
                ],
                'settings' => [
                    'webhook_url' => url('/doacoes/webhook'),
                    'success_url' => url('/doacoes/{id}/sucesso'),
                    'failure_url' => url('/doacoes'),
                ],
                'description' => 'Gateway de pagamento MercadoPago com suporte a PIX, cartão de crédito e débito.',
                'sort_order' => 1,
            ],
            [
                'gateway' => 'efipay',
                'name' => 'EFI Pay (PIX)',
                'is_active' => false,
                'is_sandbox' => true,
                'credentials' => [
                    'client_id' => '',
                    'client_secret' => '',
                ],
                'settings' => [
                    'webhook_url' => url('/doacoes/webhook'),
                    'pix_key' => '',
                ],
                'description' => 'Gateway especializado em PIX da EFI Pay (antiga Gerencianet).',
                'sort_order' => 2,
            ],
            [
                'gateway' => 'pagseguro',
                'name' => 'PagSeguro',
                'is_active' => false,
                'is_sandbox' => true,
                'credentials' => [
                    'email' => '',
                    'token' => '',
                ],
                'settings' => [
                    'webhook_url' => url('/doacoes/webhook'),
                    'redirect_url' => url('/doacoes/{id}/sucesso'),
                ],
                'description' => 'Gateway de pagamento PagSeguro com múltiplas opções de pagamento.',
                'sort_order' => 3,
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGatewayConfig::updateOrCreate(
                ['gateway' => $gateway['gateway']],
                $gateway
            );
        }
    }
}
