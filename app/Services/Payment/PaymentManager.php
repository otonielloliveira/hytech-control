<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewayConfig;
use App\Models\Payment;
use App\Services\Payment\Gateways\MercadoPagoService;
use App\Services\Payment\Gateways\EfiPayService;
use App\Services\Payment\Gateways\PagSeguroService;
use App\Services\Payment\Gateways\AsaasService;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class PaymentManager
{
    private $activeGateway;
    private $gatewayService;

    public function __construct()
    {
        $this->loadActiveGateway();
    }

    /**
     * Create a PIX payment
     */
    public function createPixPayment($payable, array $payerData, float $amount, array $options = []): array
    {
        try {
            if (!$this->activeGateway) {
                throw new Exception('Nenhum gateway de pagamento está ativo. Configure um gateway no painel administrativo.');
            }
            
            if (!$this->activeGateway->isConfigured()) {
                throw new Exception('Gateway ' . $this->activeGateway->name . ' está ativo mas não configurado. Adicione as credenciais necessárias.');
            }
            
            if (!$this->gatewayService) {
                throw new Exception('Erro ao inicializar serviço do gateway de pagamento.');
            }

            $paymentData = $this->preparePaymentData($payable, $payerData, $amount, 'pix', $options);
            
            // Create payment record
            $payment = Payment::create([
                'transaction_id' => Payment::generateTransactionId(),
                'gateway' => $this->activeGateway->gateway,
                'payment_method' => 'pix',
                'payable_type' => get_class($payable),
                'payable_id' => $payable->id,
                'amount' => $amount,
                'currency' => 'BRL',
                'status' => Payment::STATUS_PENDING,
                'payer_name' => $payerData['name'] ?? null,
                'payer_email' => $payerData['email'],
                'payer_phone' => $payerData['phone'] ?? null,
                'payer_document' => $payerData['document'] ?? null,
                'expires_at' => now()->addHour(),
                'metadata' => $options['metadata'] ?? null,
                'ip_address' => request()->ip(),
            ]);

            // Call gateway service
            $response = $this->gatewayService->createPixPayment($paymentData);

            if ($response['success']) {
                // Update payment with gateway response
                $payment->update([
                    'gateway_transaction_id' => $response['transaction_id'],
                    'pix_code' => $response['qr_code'] ?? $response['pix_code'] ?? null, // PIX payload text
                    'qr_code_url' => $response['qr_code_url'] ?? null, // URL if provided
                    'qr_code_base64' => $response['qr_code_base64'] ?? null, // Base64 image
                    'gateway_response' => $response['gateway_response'] ?? null,
                    'status' => $response['status'],
                    'expires_at' => isset($response['expires_at']) ? 
                        \Carbon\Carbon::parse($response['expires_at']) : 
                        now()->addHour(),
                ]);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'gateway_response' => $response
                ];
            } else {
                $payment->update([
                    'status' => Payment::STATUS_REJECTED,
                    'failure_reason' => $response['error'] ?? 'Erro desconhecido',
                    'gateway_response' => $response
                ]);

                return [
                    'success' => false,
                    'error' => $response['error'] ?? 'Erro ao processar pagamento',
                    'payment' => $payment
                ];
            }

        } catch (Exception $e) {
            Log::error('Payment creation error', [
                'error' => $e->getMessage(),
                'payable_type' => get_class($payable),
                'payable_id' => $payable->id,
                'amount' => $amount
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create a credit card payment
     */
    public function createCreditCardPayment($payable, array $payerData, float $amount, array $cardData, array $options = []): array
    {
        try {
            if (!$this->activeGateway) {
                throw new Exception('Nenhum gateway de pagamento está ativo. Configure um gateway no painel administrativo.');
            }
            
            if (!$this->activeGateway->isConfigured()) {
                throw new Exception('Gateway ' . $this->activeGateway->name . ' está ativo mas não configurado. Adicione as credenciais necessárias.');
            }
            
            if (!$this->gatewayService) {
                throw new Exception('Erro ao inicializar serviço do gateway de pagamento.');
            }

            $paymentData = $this->preparePaymentData($payable, $payerData, $amount, 'credit_card', $options);
            $paymentData = array_merge($paymentData, $cardData);
            
            // Create payment record
            $payment = Payment::create([
                'transaction_id' => Payment::generateTransactionId(),
                'gateway' => $this->activeGateway->gateway,
                'payment_method' => 'credit_card',
                'payable_type' => get_class($payable),
                'payable_id' => $payable->id,
                'amount' => $amount,
                'currency' => 'BRL',
                'status' => Payment::STATUS_PENDING,
                'payer_name' => $payerData['name'] ?? null,
                'payer_email' => $payerData['email'],
                'payer_phone' => $payerData['phone'] ?? null,
                'payer_document' => $payerData['document'] ?? null,
                'installments' => $cardData['installments'] ?? 1,
                'metadata' => $options['metadata'] ?? null,
                'ip_address' => request()->ip(),
            ]);

            // Call gateway service
            $response = $this->gatewayService->createCreditCardPayment($paymentData);

            if ($response['success']) {
                $payment->update([
                    'gateway_transaction_id' => $response['transaction_id'],
                    'status' => $response['status'],
                    'gateway_response' => $response['gateway_response'] ?? null,
                ]);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'gateway_response' => $response
                ];
            } else {
                $payment->update([
                    'status' => Payment::STATUS_REJECTED,
                    'failure_reason' => $response['error'] ?? 'Erro desconhecido',
                    'gateway_response' => $response
                ]);

                return [
                    'success' => false,
                    'error' => $response['error'] ?? 'Erro ao processar pagamento',
                    'payment' => $payment
                ];
            }

        } catch (Exception $e) {
            Log::error('Credit card payment error', [
                'error' => $e->getMessage(),
                'payable_type' => get_class($payable),
                'payable_id' => $payable->id,
                'amount' => $amount
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create a bank slip payment
     */
    public function createBankSlipPayment($payable, array $payerData, float $amount, array $options = []): array
    {
        try {
            if (!$this->activeGateway) {
                throw new Exception('Nenhum gateway de pagamento está ativo. Configure um gateway no painel administrativo.');
            }
            
            if (!$this->activeGateway->isConfigured()) {
                throw new Exception('Gateway ' . $this->activeGateway->name . ' está ativo mas não configurado. Adicione as credenciais necessárias.');
            }
            
            if (!$this->gatewayService) {
                throw new Exception('Erro ao inicializar serviço do gateway de pagamento.');
            }

            $paymentData = $this->preparePaymentData($payable, $payerData, $amount, 'bank_slip', $options);
            
            // Create payment record
            $payment = Payment::create([
                'transaction_id' => Payment::generateTransactionId(),
                'gateway' => $this->activeGateway->gateway,
                'payment_method' => 'bank_slip',
                'payable_type' => get_class($payable),
                'payable_id' => $payable->id,
                'amount' => $amount,
                'currency' => 'BRL',
                'status' => Payment::STATUS_PENDING,
                'payer_name' => $payerData['name'] ?? null,
                'payer_email' => $payerData['email'],
                'payer_phone' => $payerData['phone'] ?? null,
                'payer_document' => $payerData['document'] ?? null,
                'expires_at' => now()->addDays(3),
                'metadata' => $options['metadata'] ?? null,
                'ip_address' => request()->ip(),
            ]);

            // Call gateway service
            $response = $this->gatewayService->createBankSlipPayment($paymentData);

            if ($response['success']) {
                $payment->update([
                    'gateway_transaction_id' => $response['transaction_id'],
                    'status' => $response['status'],
                    'checkout_url' => $response['bank_slip_url'] ?? null,
                    'gateway_response' => $response['gateway_response'] ?? null,
                ]);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'gateway_response' => $response
                ];
            } else {
                $payment->update([
                    'status' => Payment::STATUS_REJECTED,
                    'failure_reason' => $response['error'] ?? 'Erro desconhecido',
                    'gateway_response' => $response
                ]);

                return [
                    'success' => false,
                    'error' => $response['error'] ?? 'Erro ao processar pagamento',
                    'payment' => $payment
                ];
            }

        } catch (Exception $e) {
            Log::error('Bank slip payment error', [
                'error' => $e->getMessage(),
                'payable_type' => get_class($payable),
                'payable_id' => $payable->id,
                'amount' => $amount
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Payment $payment): array
    {
        try {
            if (!$this->gatewayService || !$payment->gateway_transaction_id) {
                return [
                    'success' => false,
                    'error' => 'Gateway não configurado ou transação inválida'
                ];
            }

            $response = $this->gatewayService->getPaymentStatus($payment->gateway_transaction_id);

            if ($response['success']) {
                $oldStatus = $payment->status;
                $newStatus = $response['status'];

                if ($oldStatus !== $newStatus) {
                    $payment->update([
                        'status' => $newStatus,
                        'gateway_response' => array_merge(
                            $payment->gateway_response ?? [],
                            $response['gateway_response'] ?? []
                        )
                    ]);

                    // If payment is approved, mark as paid
                    if ($newStatus === Payment::STATUS_APPROVED && $oldStatus !== Payment::STATUS_APPROVED) {
                        $payment->update(['paid_at' => now()]);
                    }
                }

                return [
                    'success' => true,
                    'status' => $newStatus,
                    'status_changed' => $oldStatus !== $newStatus,
                    'payment' => $payment
                ];
            }

            return $response;

        } catch (Exception $e) {
            Log::error('Payment status check error', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process webhook
     */
    public function processWebhook(array $data, ?string $gatewayName = null): array
    {
        try {
            $gateway = $gatewayName ? 
                PaymentGatewayConfig::where('gateway', $gatewayName)->first() : 
                $this->activeGateway;

            if (!$gateway) {
                throw new Exception('Gateway não encontrado');
            }

            $service = $this->createGatewayService($gateway);
            $response = $service->processWebhook($data);

            if ($response['success'] && isset($response['transaction_id'])) {
                $payment = Payment::where('gateway_transaction_id', $response['transaction_id'])->first();

                if ($payment) {
                    $payment->update([
                        'status' => $response['status'],
                        'gateway_response' => array_merge(
                            $payment->gateway_response ?? [],
                            $response['gateway_response'] ?? []
                        )
                    ]);

                    if ($response['status'] === Payment::STATUS_APPROVED) {
                        $payment->update(['paid_at' => now()]);
                    }

                    return [
                        'success' => true,
                        'payment' => $payment,
                        'status' => $response['status']
                    ];
                }
            }

            return $response;

        } catch (Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'gateway' => $gatewayName,
                'data' => $data
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get active gateway configuration
     */
    public function getActiveGateway(): ?PaymentGatewayConfig
    {
        return $this->activeGateway;
    }

    /**
     * Set active gateway by name
     */
    public function setActiveGateway(string $gatewayName): bool
    {
        try {
            $gateway = PaymentGatewayConfig::where('gateway', $gatewayName)
                ->where('is_active', true)
                ->first();

            if (!$gateway || !$gateway->isConfigured()) {
                return false;
            }

            $this->activeGateway = $gateway;
            $this->gatewayService = $this->createGatewayService($gateway);

            return $this->gatewayService !== null;
        } catch (Exception $e) {
            Log::error('Erro ao definir gateway ativo', [
                'gateway' => $gatewayName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $paymentId): array
    {
        try {
            if (!$this->gatewayService) {
                throw new Exception('Nenhum gateway de pagamento ativo configurado');
            }

            return $this->gatewayService->getPaymentStatus($paymentId);
        } catch (Exception $e) {
            Log::error('Erro ao consultar status do pagamento', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test gateway connection
     */
    public function testConnection(): bool
    {
        try {
            if (!$this->gatewayService) {
                return false;
            }

            return $this->gatewayService->testConnection();
        } catch (Exception $e) {
            Log::error('Erro ao testar conexão do gateway', [
                'gateway' => $this->activeGateway?->gateway,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }    /**
     * Get supported payment methods
     */
    public function getSupportedMethods(): array
    {
        if (!$this->gatewayService) {
            return [];
        }

        return $this->gatewayService->getSupportedMethods();
    }

    /**
     * Load active gateway
     */
    private function loadActiveGateway(): void
    {
        $this->activeGateway = PaymentGatewayConfig::getActiveGateway();
        
        if ($this->activeGateway && $this->activeGateway->isConfigured()) {
            $this->gatewayService = $this->createGatewayService($this->activeGateway);
        }
    }

    /**
     * Create gateway service instance
     */
    private function createGatewayService(PaymentGatewayConfig $config): ?PaymentGatewayInterface
    {
        try {
            $gatewayConfig = [
                'api_key' => $config->getCredential('api_key'),
                'api_secret' => $config->getCredential('api_secret'),
                'public_key' => $config->getCredential('public_key'),
                'access_token' => $config->getCredential('access_token'),
                'client_id' => $config->getCredential('client_id'),
                'client_secret' => $config->getCredential('client_secret'),
                'email' => $config->getCredential('email'),
                'token' => $config->getCredential('token'),
                'is_sandbox' => $config->is_sandbox,
            ];

            return match($config->gateway) {
                'mercadopago' => new MercadoPagoService($gatewayConfig),
                'asaas' => new AsaasService($gatewayConfig),
                'efipay' => new EfiPayService($gatewayConfig),
                'pagseguro' => new PagSeguroService($gatewayConfig),
                default => throw new Exception("Gateway '{$config->gateway}' não suportado")
            };
        } catch (Exception $e) {
            Log::error('Erro ao criar serviço do gateway', [
                'gateway' => $config->gateway,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Prepare payment data for gateway
     */
    private function preparePaymentData($payable, array $payerData, float $amount, string $method, array $options): array
    {
        return [
            'amount' => $amount,
            'currency' => 'BRL',
            'description' => $options['description'] ?? $this->generateDescription($payable),
            'external_reference' => $options['external_reference'] ?? $payable->id,
            'payer' => $payerData,
            'metadata' => array_merge([
                'payable_type' => get_class($payable),
                'payable_id' => $payable->id,
                'payment_method' => $method,
            ], $options['metadata'] ?? [])
        ];
    }

    /**
     * Generate payment description
     */
    private function generateDescription($payable): string
    {
        $className = class_basename($payable);
        
        return match($className) {
            'Donation' => 'Doação para o projeto',
            'Order' => 'Pedido na loja online',
            'Course' => 'Pagamento de curso',
            default => "Pagamento - {$className} #{$payable->id}"
        };
    }
}