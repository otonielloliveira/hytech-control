<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;

class PagSeguroService implements PaymentGatewayInterface
{
    private $email;
    private $token;
    private $isSandbox;
    private $apiUrl;

    public function __construct($config)
    {
        $this->email = $config['email'] ?? null;
        $this->token = $config['token'] ?? null;
        $this->isSandbox = $config['is_sandbox'] ?? true;
        
        $this->apiUrl = $this->isSandbox 
            ? 'https://ws.sandbox.pagseguro.uol.com.br' 
            : 'https://ws.pagseguro.uol.com.br';
    }

    public function createPixPayment(array $data): array
    {
        try {
            $requestData = [
                'email' => $this->email,
                'token' => $this->token,
                'paymentMode' => 'default',
                'paymentMethod' => 'pix',
                'receiverEmail' => $this->email,
                'currency' => 'BRL',
                'itemId1' => '1',
                'itemDescription1' => $data['description'] ?? 'Pagamento',
                'itemAmount1' => number_format($data['amount'], 2, '.', ''),
                'itemQuantity1' => '1',
                'reference' => $data['external_reference'] ?? '',
                'senderName' => $data['payer']['name'] ?? '',
                'senderEmail' => $data['payer']['email'],
                'senderPhone' => $data['payer']['phone'] ?? '',
                'senderCPF' => $data['payer']['document'] ?? '',
            ];

            $response = $this->makeRequest('/v2/checkout', $requestData, 'POST');
            
            if (isset($response['code'])) {
                return [
                    'success' => true,
                    'transaction_id' => $response['code'],
                    'status' => 'pending',
                    'checkout_url' => "https://pagseguro.uol.com.br/v2/checkout/payment.html?code=" . $response['code'],
                    'gateway_response' => $response,
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Erro ao criar pagamento PagSeguro',
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ];
        }
    }

    public function createCreditCardPayment(array $data): array
    {
        try {
            $requestData = [
                'email' => $this->email,
                'token' => $this->token,
                'paymentMode' => 'default',
                'paymentMethod' => 'creditCard',
                'receiverEmail' => $this->email,
                'currency' => 'BRL',
                'itemId1' => '1',
                'itemDescription1' => $data['description'] ?? 'Pagamento',
                'itemAmount1' => number_format($data['amount'], 2, '.', ''),
                'itemQuantity1' => '1',
                'creditCardToken' => $data['card_token'],
                'installmentQuantity' => $data['installments'] ?? 1,
                'installmentValue' => number_format($data['amount'] / ($data['installments'] ?? 1), 2, '.', ''),
                'noInterestInstallmentQuantity' => 2,
                'reference' => $data['external_reference'] ?? '',
                'senderName' => $data['payer']['name'] ?? '',
                'senderEmail' => $data['payer']['email'],
                'senderPhone' => $data['payer']['phone'] ?? '',
                'senderCPF' => $data['payer']['document'] ?? '',
                'senderHash' => $data['sender_hash'] ?? '',
            ];

            $response = $this->makeRequest('/v2/transactions', $requestData, 'POST');
            
            if (isset($response['code'])) {
                return [
                    'success' => true,
                    'transaction_id' => $response['code'],
                    'status' => $this->mapStatus($response['status']),
                    'gateway_response' => $response,
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Erro ao processar cartão de crédito',
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ];
        }
    }

    public function createBankSlipPayment(array $data): array
    {
        try {
            $requestData = [
                'email' => $this->email,
                'token' => $this->token,
                'paymentMode' => 'default',
                'paymentMethod' => 'boleto',
                'receiverEmail' => $this->email,
                'currency' => 'BRL',
                'itemId1' => '1',
                'itemDescription1' => $data['description'] ?? 'Pagamento',
                'itemAmount1' => number_format($data['amount'], 2, '.', ''),
                'itemQuantity1' => '1',
                'reference' => $data['external_reference'] ?? '',
                'senderName' => $data['payer']['name'] ?? '',
                'senderEmail' => $data['payer']['email'],
                'senderPhone' => $data['payer']['phone'] ?? '',
                'senderCPF' => $data['payer']['document'] ?? '',
            ];

            $response = $this->makeRequest('/v2/transactions', $requestData, 'POST');
            
            if (isset($response['code'])) {
                return [
                    'success' => true,
                    'transaction_id' => $response['code'],
                    'status' => $this->mapStatus($response['status']),
                    'bank_slip_url' => $response['paymentLink'] ?? null,
                    'gateway_response' => $response,
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Erro ao gerar boleto',
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ];
        }
    }

    public function getPaymentStatus(string $transactionId): array
    {
        try {
            $url = "/v3/transactions/{$transactionId}?email={$this->email}&token={$this->token}";
            $response = $this->makeRequest($url, [], 'GET');
            
            return [
                'success' => true,
                'status' => $this->mapStatus($response['status']),
                'gateway_response' => $response,
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancelPayment(string $transactionId): array
    {
        try {
            $requestData = [
                'email' => $this->email,
                'token' => $this->token,
            ];
            
            $response = $this->makeRequest("/v2/transactions/cancels", $requestData, 'POST');
            
            return [
                'success' => true,
                'status' => 'cancelled',
                'gateway_response' => $response,
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function processWebhook(array $data): array
    {
        try {
            if (isset($data['notificationCode'])) {
                $url = "/v3/transactions/notifications/{$data['notificationCode']}?email={$this->email}&token={$this->token}";
                $response = $this->makeRequest($url, [], 'GET');
                
                return [
                    'success' => true,
                    'transaction_id' => $response['code'],
                    'status' => $this->mapStatus($response['status']),
                    'gateway_response' => $response,
                ];
            }
            
            return ['success' => false, 'error' => 'Invalid webhook data'];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getSupportedMethods(): array
    {
        return ['pix', 'credit_card', 'bank_slip'];
    }

    public function validateWebhookSignature(array $headers, string $body): bool
    {
        // PagSeguro webhook validation logic
        // For now, return true - implement proper validation
        return true;
    }

    public function getGatewayName(): string
    {
        return 'pagseguro';
    }

    private function makeRequest(string $endpoint, array $data = [], string $method = 'GET'): array
    {
        $url = $this->apiUrl . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Accept: application/vnd.pagseguro.com.br.v3+xml;charset=ISO-8859-1'
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("PagSeguro API error: HTTP {$httpCode}");
        }
        
        // Parse XML response
        $xml = simplexml_load_string($response);
        return json_decode(json_encode($xml), true);
    }

    private function mapStatus(string $status): string
    {
        return match($status) {
            '1' => 'pending', // Aguardando pagamento
            '2' => 'processing', // Em análise
            '3' => 'approved', // Paga
            '4' => 'approved', // Disponível
            '5' => 'processing', // Em disputa
            '6' => 'refunded', // Devolvida
            '7' => 'cancelled', // Cancelada
            default => 'pending'
        };
    }
}