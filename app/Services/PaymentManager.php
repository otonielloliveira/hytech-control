<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentGatewayConfig;
use App\Services\Gateways\PaymentGatewayInterface;
use App\Services\Gateways\MercadoPagoService;
use App\Services\Gateways\EfiPayService;
use App\Services\Gateways\PagSeguroService;
use App\Services\Payment\Gateways\AsaasService;
use App\Services\Payment\Gateways\PixManualService;
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
            'asaas' => new AsaasService(array_merge($config->credentials ?? [], ['is_sandbox' => $config->is_sandbox])),
            'pix_manual' => new PixManualService($config->credentials ?? []),
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
            // Para ASAAS e PIX Manual, passar as credenciais como array
            if (in_array($config->gateway, ['asaas', 'pix_manual'])) {
                $credentials = $config->credentials ?? [];
                
                if ($config->gateway === 'asaas') {
                    $credentials['is_sandbox'] = $config->is_sandbox;
                    $service = new AsaasService($credentials);
                    $result = $service->testConnection();
                    
                    if ($result) {
                        return [
                            'success' => true,
                            'message' => 'Conexão com ASAAS estabelecida com sucesso!',
                            'details' => 'API Key válida e conta ativa.'
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Falha ao conectar com ASAAS',
                            'details' => 'Verifique se a API Key está correta e se a conta está ativa.'
                        ];
                    }
                }
                
                if ($config->gateway === 'pix_manual') {
                    $service = new PixManualService($credentials);
                    $result = $service->testConnection();
                    
                    return [
                        'success' => $result,
                        'message' => $result ? 'Configuração PIX Manual válida!' : 'Configuração PIX Manual inválida',
                        'details' => $result ? 'Chave PIX configurada corretamente.' : 'Verifique a chave PIX configurada.'
                    ];
                }
            }
            
            // Para outros gateways, usar o método original
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