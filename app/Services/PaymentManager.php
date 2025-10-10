<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentGatewayConfig;
use App\Services\Gateways\PaymentGatewayInterface;
use App\Services\Gateways\MercadoPagoService;
use App\Services\Gateways\EfiPayService;
use App\Services\Gateways\PagSeguroService;
use Illuminate\Database\Eloquent\Model;
use Exception;

class PaymentManager
{
    /**
     * Get the active payment gateway service
     */
    public function getActiveGateway(): PaymentGatewayInterface
    {
        $config = PaymentGatewayConfig::where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        if (!$config) {
            throw new Exception('Nenhum gateway de pagamento ativo encontrado');
        }

        return $this->createGatewayService($config);
    }

    /**
     * Create gateway service instance
     */
    private function createGatewayService(PaymentGatewayConfig $config): PaymentGatewayInterface
    {
        return match($config->gateway) {
            'mercadopago' => new MercadoPagoService($config),
            'efipay' => new EfiPayService($config),
            'pagseguro' => new PagSeguroService($config),
            default => throw new Exception("Gateway '{$config->gateway}' não suportado")
        };
    }

    /**
     * Create a PIX payment
     */
    public function createPixPayment(Model $payable, float $amount, array $data = []): Payment
    {
        $gateway = $this->getActiveGateway();
        
        // Create payment record
        $payment = Payment::create([
            'payable_type' => get_class($payable),
            'payable_id' => $payable->id,
            'gateway' => $gateway->getConfig()->gateway,
            'payment_method' => 'pix',
            'amount' => $amount,
            'currency' => 'BRL',
            'status' => 'pending',
        ]);

        try {
            // Create payment in gateway
            $response = $gateway->createPixPayment($payment, $data);
            
            // Update payment with gateway response
            $payment->gateway_transaction_id = $response['payment_id'] ?? null;
            $payment->gateway_response = $response;
            $payment->pix_code = $response['pix_code'] ?? null;
            $payment->qr_code_url = $response['qr_code'] ?? null;
            $payment->expires_at = $response['expires_at'] ?? now()->addMinutes(30);
            $payment->save();

            return $payment->fresh();
        } catch (Exception $e) {
            $payment->update(['status' => 'failed']);
            throw $e;
        }
    }

    /**
     * Create a credit card payment
     */
    public function createCreditCardPayment(Model $payable, float $amount, array $cardData): Payment
    {
        $gateway = $this->getActiveGateway();
        
        $payment = Payment::create([
            'payable_type' => get_class($payable),
            'payable_id' => $payable->id,
            'gateway' => $gateway->getConfig()->gateway,
            'payment_method' => 'credit_card',
            'amount' => $amount,
            'currency' => 'BRL',
            'status' => 'pending',
        ]);

        try {
            $response = $gateway->createCreditCardPayment($payment, $cardData);
            
            $payment->gateway_transaction_id = $response['payment_id'] ?? null;
            $payment->gateway_response = $response;
            $payment->save();

            return $payment->fresh();
        } catch (Exception $e) {
            $payment->update(['status' => 'failed']);
            throw $e;
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Payment $payment): array
    {
        $config = PaymentGatewayConfig::where('gateway', $payment->gateway)->first();
        
        if (!$config) {
            throw new Exception("Gateway '{$payment->gateway}' não encontrado");
        }

        $gateway = $this->createGatewayService($config);
        
        return $gateway->checkPaymentStatus($payment);
    }

    /**
     * Process webhook
     */
    public function processWebhook(string $gateway, array $data): bool
    {
        $config = PaymentGatewayConfig::where('gateway', $gateway)->first();
        
        if (!$config) {
            throw new Exception("Gateway '{$gateway}' não encontrado");
        }

        $gatewayService = $this->createGatewayService($config);
        
        return $gatewayService->processWebhook($data);
    }

    /**
     * Get available gateways
     */
    public function getAvailableGateways(): array
    {
        return PaymentGatewayConfig::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'gateway', 'name', 'description'])
            ->toArray();
    }

    /**
     * Test gateway connection
     */
    public function testGatewayConnection(PaymentGatewayConfig $config): array
    {
        try {
            $gateway = $this->createGatewayService($config);
            return $gateway->testConnection();
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao testar conexão',
                'details' => $e->getMessage()
            ];
        }
    }
}