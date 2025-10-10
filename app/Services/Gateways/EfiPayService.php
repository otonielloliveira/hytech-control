<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use App\Models\PaymentGatewayConfig;
use Exception;

class EfiPayService implements PaymentGatewayInterface
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
        // Mock EFI Pay PIX payment
        return [
            'payment_id' => 'EFI_' . uniqid(),
            'status' => 'pending',
            'pix_code' => $this->generateMockPixCode(),
            'qr_code' => $this->generateMockQrCode($payment->amount),
            'expires_at' => now()->addMinutes(30),
            'amount' => $payment->amount,
            'gateway_response' => [
                'mock' => true,
                'gateway' => 'EFI Pay',
                'message' => 'Mock PIX payment created'
            ]
        ];
    }

    public function createCreditCardPayment(Payment $payment, array $cardData): array
    {
        // EFI Pay typically focuses on PIX, but can handle cards
        return [
            'payment_id' => 'EFI_CC_' . uniqid(),
            'status' => 'approved',
            'transaction_id' => uniqid(),
            'amount' => $payment->amount,
            'gateway_response' => [
                'mock' => true,
                'gateway' => 'EFI Pay',
                'message' => 'Mock credit card payment'
            ]
        ];
    }

    public function createBankSlipPayment(Payment $payment, array $data = []): array
    {
        return [
            'payment_id' => 'EFI_SLIP_' . uniqid(),
            'status' => 'pending',
            'bank_slip_url' => 'https://mock-efi.com/bank-slip.pdf',
            'expires_at' => now()->addDays(3),
            'amount' => $payment->amount,
            'gateway_response' => [
                'mock' => true,
                'gateway' => 'EFI Pay',
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
                'gateway' => 'EFI Pay',
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
        return !empty($credentials['client_id']) && !empty($credentials['client_secret']);
    }

    public function testConnection(): array
    {
        try {
            if (!$this->validateConfig()) {
                return [
                    'success' => false,
                    'message' => 'Credenciais não configuradas',
                    'details' => 'Client ID e Client Secret são obrigatórios para EFI Pay'
                ];
            }

            $credentials = $this->config->credentials;
            
            // Simula validação básica
            if (strlen($credentials['client_id']) < 5 || strlen($credentials['client_secret']) < 10) {
                return [
                    'success' => false,
                    'message' => 'Credenciais inválidas',
                    'details' => 'Client ID ou Client Secret muito curtos'
                ];
            }

            // Em produção: autenticar com EFI Pay e obter token de acesso
            return [
                'success' => true,
                'message' => 'Conexão com EFI Pay estabelecida com sucesso',
                'details' => [
                    'gateway' => 'EFI Pay (antiga Gerencianet)',
                    'environment' => $this->config->is_sandbox ? 'Sandbox' : 'Produção',
                    'client_id' => substr($credentials['client_id'], 0, 8) . '...',
                    'speciality' => 'PIX Payments',
                    'payment_methods' => ['PIX', 'Cartão de Crédito', 'Boleto'],
                    'timestamp' => now()->format('d/m/Y H:i:s'),
                    'mock_mode' => true
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Falha na conexão com EFI Pay',
                'details' => 'Erro técnico: ' . $e->getMessage()
            ];
        }
    }

    private function generateMockPixCode(): string
    {
        return '00020126580014BR.GOV.BCB.PIX013' . rand(10000000000, 99999999999) . '5204000053039865802BR5925' . 'EFI PAY MOCK' . '6014SAO PAULO62' . sprintf('%02d', 16) . '0512' . uniqid() . '6304' . substr(md5(uniqid()), 0, 4);
    }

    private function generateMockQrCode(float $amount): string
    {
        return "data:image/svg+xml;base64," . base64_encode(
            '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                <rect width="200" height="200" fill="white"/>
                <rect x="10" y="10" width="180" height="180" fill="none" stroke="black" stroke-width="2"/>
                <text x="100" y="100" text-anchor="middle" font-family="Arial" font-size="12">
                    EFI Pay PIX
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