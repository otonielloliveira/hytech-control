<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;

class PixManualService implements PaymentGatewayInterface
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Create a PIX payment (Manual)
     */
    public function createPixPayment(array $paymentData): array
    {
        try {
            // Para PIX Manual, usamos uma chave PIX fixa configurada no admin
            $pixKey = $this->config['pix_key'] ?? null;
            $pixKeyType = $this->config['pix_key_type'] ?? 'cpf';
            $beneficiaryName = $this->config['pix_beneficiary_name'] ?? 'Destinatário';
            
            if (!$pixKey) {
                throw new Exception('Chave PIX não configurada. Configure a chave PIX no painel administrativo.');
            }

            // Gerar ID único para a transação
            $transactionId = 'PIX_MANUAL_' . time() . '_' . rand(1000, 9999);

            // Para PIX Manual, o QR Code seria gerado com a chave PIX e o valor
            // Aqui vamos retornar apenas a chave PIX para o usuário copiar
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'pix_code' => $pixKey, // A chave PIX para copiar e colar
                'qr_code' => $pixKey, // Mesma coisa
                'qr_code_url' => null, // Não temos URL para PIX manual
                'qr_code_base64' => null, // Não geramos QR Code para PIX manual
                'status' => 'pending',
                'expires_at' => now()->addHour()->toIso8601String(),
                'gateway_response' => [
                    'type' => 'pix_manual',
                    'pix_key' => $pixKey,
                    'pix_key_type' => $pixKeyType,
                    'beneficiary_name' => $beneficiaryName,
                    'amount' => $paymentData['amount'],
                ],
                'message' => 'PIX Manual gerado. Use a chave PIX para realizar o pagamento.',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a credit card payment
     */
    public function createCreditCardPayment(array $paymentData): array
    {
        return [
            'success' => false,
            'error' => 'PIX Manual não suporta pagamento com cartão de crédito.',
        ];
    }

    /**
     * Create a bank slip payment
     */
    public function createBankSlipPayment(array $data): array
    {
        return [
            'success' => false,
            'error' => 'PIX Manual não suporta pagamento com boleto.',
        ];
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $transactionId): array
    {
        // Para PIX Manual, a confirmação deve ser feita manualmente no admin
        return [
            'success' => true,
            'status' => 'pending',
            'status_changed' => false,
            'message' => 'Aguardando confirmação manual do pagamento.',
        ];
    }

    /**
     * Check payment status (alias)
     */
    public function checkPaymentStatus(string $transactionId): array
    {
        return $this->getPaymentStatus($transactionId);
    }

    /**
     * Cancel a payment
     */
    public function cancelPayment(string $transactionId): array
    {
        return [
            'success' => true,
            'message' => 'Pagamento cancelado.',
        ];
    }

    /**
     * Refund a payment
     */
    public function refundPayment(string $transactionId, ?float $amount = null): array
    {
        return [
            'success' => false,
            'error' => 'PIX Manual não suporta estornos automáticos. Realize o estorno manualmente.',
        ];
    }

    /**
     * Process webhook notification
     */
    public function processWebhook(array $data): array
    {
        // PIX Manual não recebe webhooks
        return [
            'success' => false,
            'error' => 'PIX Manual não processa webhooks.',
        ];
    }

    /**
     * Get supported payment methods
     */
    public function getSupportedMethods(): array
    {
        return ['pix'];
    }

    /**
     * Get available payment methods (alias)
     */
    public function getAvailablePaymentMethods(): array
    {
        return $this->getSupportedMethods();
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhookSignature(array $headers, string $body): bool
    {
        // PIX Manual não usa webhooks
        return false;
    }

    /**
     * Get gateway name
     */
    public function getGatewayName(): string
    {
        return 'PIX Manual';
    }

    /**
     * Get gateway name (alias)
     */
    public function getName(): string
    {
        return $this->getGatewayName();
    }

    /**
     * Test gateway connection
     */
    public function testConnection(): bool
    {
        // PIX Manual sempre retorna true se tiver uma chave configurada
        return !empty($this->config['pix_key']);
    }

    /**
     * Validate gateway configuration
     */
    public function validateConfig(): bool
    {
        return !empty($this->config['pix_key']);
    }
}
