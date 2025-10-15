<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    public function processPayment(Order $order, PaymentGatewayConfig $paymentMethod, array $paymentData = [])
    {
        try {
            switch ($paymentMethod->gateway) {
                case 'pix':
                    return $this->processPix($order, $paymentMethod, $paymentData);
                    
                case 'boleto':
                    return $this->processBoleto($order, $paymentMethod, $paymentData);
                    
                case 'card':
                    return $this->processCard($order, $paymentMethod, $paymentData);
                    
                case 'asaas':
                    return $this->processAsaas($order, $paymentMethod, $paymentData);
                    
                case 'mercadopago':
                    return $this->processMercadoPago($order, $paymentMethod, $paymentData);
                    
                case 'pagseguro':
                    return $this->processPagSeguro($order, $paymentMethod, $paymentData);
                    
                default:
                    throw new \Exception('Gateway de pagamento não suportado: ' . $paymentMethod->gateway);
            }
        } catch (\Exception $e) {
            Log::error('Erro no processamento do pagamento', [
                'order_id' => $order->id,
                'payment_method' => $paymentMethod->gateway,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro no processamento do pagamento: ' . $e->getMessage(),
                'transaction_id' => null
            ];
        }
    }

    private function processPix(Order $order, PaymentMethod $paymentMethod, array $paymentData)
    {
        $config = $paymentMethod->getGatewayConfig();
        
        // Gerar QR Code PIX (implementação simplificada)
        $pixData = [
            'pix_key' => $config['pix_key'] ?? '',
            'amount' => $order->total,
            'description' => "Pedido {$order->order_number}",
            'beneficiary_name' => $config['beneficiary_name'] ?? 'Loja',
        ];
        
        // Simular geração de QR Code
        $qrCodeData = $this->generatePixQrCode($pixData);
        
        return [
            'success' => true,
            'message' => 'PIX gerado com sucesso',
            'transaction_id' => 'PIX_' . time(),
            'payment_url' => null,
            'qr_code' => $qrCodeData,
            'expires_at' => now()->addHours(24),
        ];
    }

    private function processBoleto(Order $order, PaymentMethod $paymentMethod, array $paymentData)
    {
        $config = $paymentMethod->getGatewayConfig();
        
        // Gerar dados do boleto
        $boletoData = [
            'amount' => $order->total,
            'due_date' => now()->addDays(3)->format('Y-m-d'),
            'description' => "Pedido {$order->order_number}",
            'customer' => [
                'name' => $order->billing_address['name'] ?? '',
                'document' => $order->billing_address['document'] ?? '',
                'email' => $order->billing_address['email'] ?? '',
            ]
        ];
        
        // Simular geração de boleto
        $boletoNumber = $this->generateBoletoNumber();
        
        return [
            'success' => true,
            'message' => 'Boleto gerado com sucesso',
            'transaction_id' => 'BOLETO_' . $boletoNumber,
            'payment_url' => route('payment.boleto', ['order' => $order->id, 'token' => $boletoNumber]),
            'barcode' => $this->generateBarcode($boletoData),
            'due_date' => $boletoData['due_date'],
        ];
    }

    private function processCard(Order $order, PaymentMethod $paymentMethod, array $paymentData)
    {
        // Implementação básica para processamento de cartão
        // Em produção, integrar com processadores como Stripe, PagSeguro, etc.
        
        $cardData = $paymentData['card'] ?? [];
        
        // Validar dados do cartão (implementação simplificada)
        if (empty($cardData['number']) || empty($cardData['cvv']) || empty($cardData['expiry'])) {
            throw new \Exception('Dados do cartão incompletos');
        }
        
        // Simular processamento
        $transactionId = 'CARD_' . time() . '_' . rand(1000, 9999);
        
        return [
            'success' => true,
            'message' => 'Pagamento processado com sucesso',
            'transaction_id' => $transactionId,
            'authorization_code' => 'AUTH_' . rand(100000, 999999),
            'installments' => $paymentData['installments'] ?? 1,
        ];
    }

    private function processAsaas(Order $order, PaymentGatewayConfig $paymentMethod, array $paymentData)
    {
        $config = $paymentMethod->getActiveGateway();
        $apiKey = $config['api_key'] ?? '';
        $environment = $config['environment'] ?? 'sandbox';
        
        $baseUrl = $environment === 'production' 
            ? 'https://www.asaas.com/api/v3' 
            : 'https://sandbox.asaas.com/api/v3';
        
        // Criar cobrança no Asaas
        $response = Http::withHeaders([
            'access_token' => $apiKey,
            'Content-Type' => 'application/json'
        ])->post($baseUrl . '/payments', [
            'customer' => $this->getAsaasCustomerId($order, $config),
            'billingType' => $paymentData['billing_type'] ?? 'PIX',
            'value' => $order->total,
            'dueDate' => now()->addDays(1)->format('Y-m-d'),
            'description' => "Pedido {$order->order_number}",
            'externalReference' => $order->id,
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            Log::info('Cobrança Asaas criada', ['response' => $data]);
            return [
                'success' => true,
                'message' => 'Cobrança criada com sucesso',
                'transaction_id' => $data['id'],
                'payment_url' => $data['invoiceUrl'] ?? null,
                'qr_code' => $data['pixQrCodeId'] ?? null,
            ];
        }
        
        throw new \Exception('Erro na API do Asaas: ' . $response->body());
    }

    private function processMercadoPago(Order $order, PaymentMethod $paymentMethod, array $paymentData)
    {
        $config = $paymentMethod->getGatewayConfig();
        $accessToken = $config['access_token'] ?? '';
        
        // Criar preferência de pagamento
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json'
        ])->post('https://api.mercadopago.com/checkout/preferences', [
            'items' => [
                [
                    'title' => "Pedido {$order->order_number}",
                    'quantity' => 1,
                    'unit_price' => (float)$order->total,
                ]
            ],
            'payer' => [
                'email' => $order->billing_address['email'] ?? '',
                'name' => $order->billing_address['name'] ?? '',
            ],
            'back_urls' => [
                'success' => route('payment.success', $order->id),
                'failure' => route('payment.failure', $order->id),
                'pending' => route('payment.pending', $order->id),
            ],
            'auto_return' => 'approved',
            'external_reference' => $order->id,
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            
            return [
                'success' => true,
                'message' => 'Preferência criada com sucesso',
                'transaction_id' => $data['id'],
                'payment_url' => $data['init_point'],
                'sandbox_url' => $data['sandbox_init_point'] ?? null,
            ];
        }
        
        throw new \Exception('Erro na API do Mercado Pago: ' . $response->body());
    }

    private function processPagSeguro(Order $order, PaymentMethod $paymentMethod, array $paymentData)
    {
        $config = $paymentMethod->getGatewayConfig();
        $token = $config['token'] ?? '';
        $email = $config['email'] ?? '';
        
        // Implementação básica do PagSeguro
        // Em produção, usar a biblioteca oficial do PagSeguro
        
        return [
            'success' => true,
            'message' => 'Integração PagSeguro em desenvolvimento',
            'transaction_id' => 'PAGSEGURO_' . time(),
            'payment_url' => '#', // URL do PagSeguro
        ];
    }

    private function generatePixQrCode(array $pixData)
    {
        // Implementação simplificada do QR Code PIX
        // Em produção, usar biblioteca específica para PIX
        
        $qrString = "00020101021226{$pixData['pix_key']}5204000053039865802BR";
        $qrString .= "6009{$pixData['beneficiary_name']}";
        $qrString .= "62070503***6304"; // CRC será calculado
        
        return [
            'qr_code' => $qrString,
            'qr_code_image' => 'data:image/png;base64,' . base64_encode('QR_CODE_IMAGE_DATA'),
            'copy_paste' => $qrString,
        ];
    }

    private function generateBoletoNumber()
    {
        return date('Ymd') . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    private function generateBarcode(array $boletoData)
    {
        // Gerar código de barras do boleto (implementação simplificada)
        return '23791.76105.60001.123456.78901.234567.89';
    }

    private function getAsaasCustomerId(Order $order, array $config)
    {
        // Em produção, verificar se o cliente já existe no Asaas
        // e criar um novo se necessário
        return 'CUSTOMER_ID_PLACEHOLDER';
    }

    public function verifyWebhook(string $gateway, array $data)
    {
        switch ($gateway) {
            case 'asaas':
                return $this->verifyAsaasWebhook($data);
                
            case 'mercadopago':
                return $this->verifyMercadoPagoWebhook($data);
                
            case 'pagseguro':
                return $this->verifyPagSeguroWebhook($data);
                
            default:
                return false;
        }
    }

    private function verifyAsaasWebhook(array $data)
    {
        // Implementar verificação de webhook do Asaas
        return true;
    }

    private function verifyMercadoPagoWebhook(array $data)
    {
        // Implementar verificação de webhook do Mercado Pago
        return true;
    }

    private function verifyPagSeguroWebhook(array $data)
    {
        // Implementar verificação de webhook do PagSeguro
        return true;
    }
}