<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use App\Models\PaymentGatewayConfig;
use Exception;

class PagSeguroService implements PaymentGatewayInterface
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
        // Mock PagSeguro PIX payment
        return [
            'payment_id' => 'PS_' . uniqid(),
            'status' => 'pending',
            'pix_code' => $this->generateMockPixCode(),
            'qr_code' => $this->generateMockQrCode($payment->amount),
            'expires_at' => now()->addMinutes(30),
            'amount' => $payment->amount,
            'gateway_response' => [
                'mock' => true,
                'gateway' => 'PagSeguro',
                'message' => 'Mock PIX payment created'
            ]
        ];
    }

    public function createCreditCardPayment(Payment $payment, array $cardData): array
    {
        return [
            'payment_id' => 'PS_CC_' . uniqid(),
            'status' => 'approved',
            'transaction_id' => uniqid(),
            'amount' => $payment->amount,
            'gateway_response' => [
                'mock' => true,
                'gateway' => 'PagSeguro',
                'message' => 'Mock credit card payment'
            ]
        ];
    }

    public function createBankSlipPayment(Payment $payment, array $data = []): array
    {
        return [
            'payment_id' => 'PS_SLIP_' . uniqid(),
            'status' => 'pending',
            'bank_slip_url' => 'https://mock-pagseguro.com/bank-slip.pdf',
            'expires_at' => now()->addDays(3),
            'amount' => $payment->amount,
            'gateway_response' => [
                'mock' => true,
                'gateway' => 'PagSeguro',
                'message' => 'Mock bank slip generated'
            ]
        ];
    }

    public function checkPaymentStatus(Payment $payment): array
    {
        return [
            'status' => 'pending',
            'is_paid' => false,
            'paid_at' => null,
            'gateway_response' => [
                'mock' => true,
                'gateway' => 'PagSeguro',
                'message' => 'Mock status check'
            ]
        ];
    }

    public function processWebhook(array $data): bool
    {
        return true;
    }

    public function validateConfig(): bool
    {
        $credentials = $this->config->credentials;
        return !empty($credentials['email']) && !empty($credentials['token']);
    }

    public function testConnection(): array
    {
        try {
            if (!$this->validateConfig()) {
                return [
                    'success' => false,
                    'message' => 'Credenciais não configuradas',
                    'details' => 'Email e Token são obrigatórios para PagSeguro'
                ];
            }

            $credentials = $this->config->credentials;
            
            // Simula validação do email
            if (!filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'message' => 'Email inválido',
                    'details' => 'O email fornecido não é válido'
                ];
            }
            
            // Simula validação do token
            if (strlen($credentials['token']) < 15) {
                return [
                    'success' => false,
                    'message' => 'Token inválido',
                    'details' => 'Token muito curto ou inválido'
                ];
            }

            // Em produção: fazer uma chamada para a API do PagSeguro
            return [
                'success' => true,
                'message' => 'Conexão com PagSeguro estabelecida com sucesso',
                'details' => [
                    'gateway' => 'PagSeguro UOL',
                    'environment' => $this->config->is_sandbox ? 'Sandbox' : 'Produção',
                    'email' => $credentials['email'],
                    'token_prefix' => substr($credentials['token'], 0, 8) . '...',
                    'payment_methods' => ['PIX', 'Cartão de Crédito', 'Boleto', 'Transferência'],
                    'timestamp' => now()->format('d/m/Y H:i:s'),
                    'api_version' => 'v4',
                    'mock_mode' => true
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Falha na conexão com PagSeguro',
                'details' => 'Erro técnico: ' . $e->getMessage()
            ];
        }
    }

    private function generateMockPixCode(): string
    {
        return '00020126580014BR.GOV.BCB.PIX013' . rand(10000000000, 99999999999) . '5204000053039865802BR5925' . 'PAGSEGURO MOCK' . '6014SAO PAULO62' . sprintf('%02d', 16) . '0512' . uniqid() . '6304' . substr(md5(uniqid()), 0, 4);
    }

    private function generateMockQrCode(float $amount): string
    {
        return "data:image/svg+xml;base64," . base64_encode(
            '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                <rect width="200" height="200" fill="white"/>
                <circle cx="100" cy="100" r="80" fill="none" stroke="#007bff" stroke-width="3"/>
                <text x="100" y="90" text-anchor="middle" font-family="Arial" font-size="12">
                    PagSeguro PIX
                </text>
                <text x="100" y="110" text-anchor="middle" font-family="Arial" font-size="10">
                    R$ ' . number_format($amount, 2, ',', '.') . '
                </text>
                <text x="100" y="130" text-anchor="middle" font-family="Arial" font-size="8">
                    (Mock Payment)
                </text>
            </svg>'
        );
    }
}