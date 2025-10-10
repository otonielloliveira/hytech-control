<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use App\Models\PaymentGatewayConfig;
use Exception;

class MercadoPagoService implements PaymentGatewayInterface
{
    private PaymentGatewayConfig $config;

    public function __construct(PaymentGatewayConfig $config)
    {
        $this->config = $config;
    }

    public function getConfig(): PaymentGatewayConfig
    {
        return $this->config;
    }

    public function createPixPayment(Payment $payment, array $data = []): array
    {
        // For now, return a mock response
        // In production, you would integrate with MercadoPago SDK
        
        $pixCode = $this->generateMockPixCode();
        $qrCode = $this->generateMockQrCode($payment->amount);
        
        return [
            'payment_id' => 'MP_' . uniqid(),
            'status' => 'pending',
            'pix_code' => $pixCode,
            'qr_code' => $qrCode,
            'expires_at' => now()->addMinutes(30),
            'amount' => $payment->amount,
            'gateway_response' => [
                'mock' => true,
                'message' => 'This is a mock payment for testing purposes'
            ]
        ];
    }

    public function createCreditCardPayment(Payment $payment, array $cardData): array
    {
        // Mock credit card payment
        return [
            'payment_id' => 'MP_CC_' . uniqid(),
            'status' => 'approved',
            'transaction_id' => uniqid(),
            'amount' => $payment->amount,
            'gateway_response' => [
                'mock' => true,
                'message' => 'Mock credit card payment approved'
            ]
        ];
    }

    public function createBankSlipPayment(Payment $payment, array $data = []): array
    {
        // Mock bank slip
        return [
            'payment_id' => 'MP_SLIP_' . uniqid(),
            'status' => 'pending',
            'bank_slip_url' => 'https://example.com/bank-slip.pdf',
            'expires_at' => now()->addDays(3),
            'amount' => $payment->amount,
            'gateway_response' => [
                'mock' => true,
                'message' => 'Mock bank slip generated'
            ]
        ];
    }

    public function checkPaymentStatus(Payment $payment): array
    {
        // Mock status check
        return [
            'status' => 'pending',
            'is_paid' => false,
            'paid_at' => null,
            'gateway_response' => [
                'mock' => true,
                'message' => 'Mock status check'
            ]
        ];
    }

    public function processWebhook(array $data): bool
    {
        // Mock webhook processing
        return true;
    }

    public function validateConfig(): bool
    {
        $credentials = $this->config->credentials;
        return !empty($credentials['access_token']);
    }

    public function testConnection(): array
    {
        try {
            if (!$this->validateConfig()) {
                return [
                    'success' => false,
                    'message' => 'Credenciais não configuradas',
                    'details' => 'Access Token é obrigatório para conectar ao MercadoPago'
                ];
            }

            $credentials = $this->config->credentials;
            $accessToken = $credentials['access_token'];
            
            // Simula validação do token
            if (strlen($accessToken) < 10) {
                return [
                    'success' => false,
                    'message' => 'Token inválido',
                    'details' => 'Access Token muito curto ou inválido'
                ];
            }

            // Em produção, aqui faria uma chamada real para a API do MercadoPago
            // Por exemplo: GET /v1/payment_methods para verificar se o token é válido
            
            return [
                'success' => true,
                'message' => 'Conexão com MercadoPago estabelecida com sucesso',
                'details' => [
                    'gateway' => 'MercadoPago',
                    'environment' => $this->config->is_sandbox ? 'Sandbox' : 'Produção',
                    'token_prefix' => substr($accessToken, 0, 10) . '...',
                    'payment_methods' => ['PIX', 'Cartão de Crédito', 'Cartão de Débito'],
                    'timestamp' => now()->format('d/m/Y H:i:s'),
                    'api_version' => 'v1',
                    'mock_mode' => true
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Falha na conexão com MercadoPago',
                'details' => 'Erro técnico: ' . $e->getMessage()
            ];
        }
    }

    private function generateMockPixCode(): string
    {
        // Generate a mock PIX code
        return '00020126580014BR.GOV.BCB.PIX013' . rand(10000000000, 99999999999) . '5204000053039865802BR5925' . 'MOCK MERCHANT' . '6014SAO PAULO62' . sprintf('%02d', 16) . '0512' . uniqid() . '6304' . substr(md5(uniqid()), 0, 4);
    }

    private function generateMockQrCode(float $amount): string
    {
        // Return a simple data URL for QR code (in production, generate actual QR code)
        return "data:image/svg+xml;base64," . base64_encode(
            '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                <rect width="200" height="200" fill="white"/>
                <text x="100" y="100" text-anchor="middle" font-family="Arial" font-size="12">
                    QR Code PIX
                </text>
                <text x="100" y="120" text-anchor="middle" font-family="Arial" font-size="10">
                    R$ ' . number_format($amount, 2, ',', '.') . '
                </text>
                <text x="100" y="140" text-anchor="middle" font-family="Arial" font-size="8">
                    (Mock Payment)
                </text>
            </svg>'
        );
    }
}