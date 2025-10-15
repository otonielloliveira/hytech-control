<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use GuzzleHttp\Client;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AsaasService implements PaymentGatewayInterface
{
    private $apiKey;
    private $baseUrl;
    private $client;

    public function __construct($config)
    {
        $this->apiKey = $config['api_key'] ?? null;
        $this->baseUrl = $config['is_sandbox'] ?? true 
            ? 'https://sandbox.asaas.com/api/v3'
            : 'https://www.asaas.com/api/v3';
        
        // Configure client with minimal settings
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'verify' => true,
        ]);
    }

    /**
     * Make a request to ASAAS API with proper headers using Laravel Http facade
     */
    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;
        
        $http = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(30);

        $response = match(strtoupper($method)) {
            'GET' => $http->get($url, $data),
            'POST' => $http->post($url, $data),
            'PUT' => $http->put($url, $data),
            'DELETE' => $http->delete($url, $data),
            default => throw new Exception("Unsupported HTTP method: {$method}")
        };
        
        return [
            'status_code' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
            'successful' => $response->successful()
        ];
    }

    public function createPixPayment(array $data): array
    {
        try {
            $customer = $this->getOrCreateCustomer($data['payer']);
            
            $requestData = [
                'customer' => $customer['id'],
                'billingType' => 'PIX',
                'value' => (float) $data['amount'],
                'dueDate' => now()->addDays(1)->format('Y-m-d'),
                'description' => $data['description'] ?? 'Pagamento PIX',
                'externalReference' => $data['external_reference'] ?? null,
            ];

            if (isset($data['metadata'])) {
                $requestData['metadata'] = $data['metadata'];
            }

            Log::info('ASAAS PIX Request Data:', $requestData);

            $response = $this->makeRequest('POST', '/payments', $requestData);

            if (!$response['successful'] || !isset($response['json']['id'])) {
                throw new Exception('Invalid response from ASAAS API: ' . $response['body']);
            }

            $result = $response['json'];

            // Generate PIX QR Code
            $pixData = $this->generatePixQrCode($result['id']);

            return [
                'success' => true,
                'payment_id' => $result['id'],
                'transaction_id' => $result['id'], // Alias for compatibility
                'status' => $this->mapStatus($result['status']),
                'qr_code' => $pixData['qr_code'] ?? null,
                'qr_code_base64' => $pixData['qr_code_base64'] ?? null,
                'expires_at' => $result['dueDate'] ?? null,
                'gateway_response' => $result,
            ];

        } catch (Exception $e) {
            Log::error('ASAAS PIX General Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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
            $customer = $this->getOrCreateCustomer($data['payer']);
            
            $requestData = [
                'customer' => $customer['id'],
                'billingType' => 'CREDIT_CARD',
                'value' => (float) $data['amount'],
                'dueDate' => now()->addDays(1)->format('Y-m-d'),
                'description' => $data['description'] ?? 'Pagamento Cartão de Crédito',
                'externalReference' => $data['external_reference'] ?? null,
                'installmentCount' => (int) ($data['installments'] ?? 1),
                'creditCard' => [
                    'holderName' => $data['card']['holder_name'],
                    'number' => $data['card']['number'],
                    'expiryMonth' => $data['card']['expiry_month'],
                    'expiryYear' => $data['card']['expiry_year'],
                    'ccv' => $data['card']['security_code'],
                ],
                'creditCardHolderInfo' => [
                    'name' => $data['payer']['name'] ?? $data['card']['holder_name'],
                    'email' => $data['payer']['email'],
                    'cpfCnpj' => $data['payer']['document'] ?? '',
                    'postalCode' => $data['payer']['address']['zip_code'] ?? '',
                    'addressNumber' => $data['payer']['address']['number'] ?? '',
                    'phone' => $data['payer']['phone'] ?? '',
                ]
            ];

            if (isset($data['metadata'])) {
                $requestData['metadata'] = $data['metadata'];
            }

            Log::info('ASAAS Credit Card Request Data:', array_merge($requestData, [
                'creditCard' => array_merge($requestData['creditCard'], [
                    'number' => '****' . substr($requestData['creditCard']['number'], -4),
                    'ccv' => '***'
                ])
            ]));

            $response = $this->makeRequest('POST', '/payments', $requestData);

            if (!$response['successful'] || !isset($response['json']['id'])) {
                throw new Exception('Invalid response from ASAAS API: ' . $response['body']);
            }

            $result = $response['json'];

            return [
                'success' => true,
                'payment_id' => $result['id'],
                'transaction_id' => $result['id'], // Alias for compatibility
                'status' => $this->mapStatus($result['status']),
                'installments' => $result['installmentCount'] ?? 1,
                'gateway_response' => $result,
            ];

        } catch (Exception $e) {
            Log::error('ASAAS Credit Card General Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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
            $customer = $this->getOrCreateCustomer($data['payer']);
            
            $requestData = [
                'customer' => $customer['id'],
                'billingType' => 'BOLETO',
                'value' => (float) $data['amount'],
                'dueDate' => now()->addDays(3)->format('Y-m-d'), // Boleto usually has longer due date
                'description' => $data['description'] ?? 'Pagamento Boleto',
                'externalReference' => $data['external_reference'] ?? null,
            ];

            if (isset($data['metadata'])) {
                $requestData['metadata'] = $data['metadata'];
            }

            Log::info('ASAAS Bank Slip Request Data:', $requestData);

            $response = $this->makeRequest('POST', '/payments', $requestData);

            if (!$response['successful'] || !isset($response['json']['id'])) {
                throw new Exception('Invalid response from ASAAS API: ' . $response['body']);
            }

            $result = $response['json'];

            return [
                'success' => true,
                'payment_id' => $result['id'],
                'transaction_id' => $result['id'], // Alias for compatibility
                'status' => $this->mapStatus($result['status']),
                'bank_slip_url' => $result['bankSlipUrl'] ?? null,
                'bar_code' => $result['identificationField'] ?? null,
                'expires_at' => $result['dueDate'] ?? null,
                'gateway_response' => $result,
            ];

        } catch (Exception $e) {
            Log::error('ASAAS Bank Slip General Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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
            $response = $this->makeRequest('GET', "/payments/{$transactionId}");
            
            if (!$response['successful'] || !isset($response['json']['id'])) {
                throw new Exception('Invalid response from ASAAS API: ' . $response['body']);
            }
            
            $result = $response['json'];

            return [
                'success' => true,
                'payment_id' => $result['id'],
                'status' => $this->mapStatus($result['status']),
                'amount' => $result['value'],
                'gateway_response' => $result,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ];
        }
    }

    public function cancelPayment(string $transactionId): array
    {
        try {
            $response = $this->makeRequest('DELETE', "/payments/{$transactionId}");
            
            if (!$response['successful'] || !isset($response['json']['id'])) {
                throw new Exception('Invalid response from ASAAS API: ' . $response['body']);
            }
            
            $result = $response['json'];

            return [
                'success' => true,
                'payment_id' => $result['id'],
                'transaction_id' => $result['id'], // Alias for compatibility
                'transaction_id' => $result['id'], // Alias for compatibility
                'status' => $this->mapStatus($result['status']),
                'gateway_response' => $result,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ];
        }
    }

    public function processWebhook(array $data): array
    {
        try {
            // ASAAS webhook structure
            $paymentId = $data['payment']['id'] ?? null;
            if (!$paymentId) {
                return [
                    'success' => false,
                    'error' => 'Invalid webhook data: missing payment ID',
                ];
            }

            $status = $this->getPaymentStatus($paymentId);
            
            return [
                'success' => true,
                'transaction_id' => $paymentId,
                'status' => $status['status'] ?? 'unknown',
                'external_reference' => $data['payment']['externalReference'] ?? null,
                'gateway_response' => $data,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function testConnection(): bool
    {
        try {
            $result = $this->makeRequest('GET', '/myAccount');
            
            Log::info('ASAAS Connection Test:', [
                'status_code' => $result['status_code'],
                'successful' => $result['successful'],
                'has_json_response' => is_array($result['json']),
                'has_account_name' => isset($result['json']['name']),
                'response_preview' => substr($result['body'], 0, 200)
            ]);
            
            return $result['successful'] && is_array($result['json']) && isset($result['json']['name']);
        } catch (Exception $e) {
            Log::error('ASAAS Connection Test Error:', [
                'error' => $e->getMessage(),
                'api_key_preview' => substr($this->apiKey, 0, 20) . '...',
                'base_url' => $this->baseUrl
            ]);
            return false;
        }
    }

    /**
     * Create ASAAS Checkout (Hosted Checkout Page)
     */
    public function createCheckout(array $data): array
    {
        try {
            $requestData = [
                'billingTypes' => $data['billing_types'] ?? ['PIX', 'CREDIT_CARD'],
                'chargeTypes' => $data['charge_types'] ?? ['DETACHED'],
                'minutesToExpire' => $data['minutes_to_expire'] ?? 60,
                'items' => $data['items'],
            ];

            // Add callback URLs if provided
            if (isset($data['callback'])) {
                $requestData['callback'] = $data['callback'];
            }

            // Add customer data if provided
            if (isset($data['customer_data'])) {
                $requestData['customerData'] = $data['customer_data'];
            }

            // Add installment options for credit card
            if (isset($data['installment'])) {
                $requestData['installment'] = $data['installment'];
            }

            // Add external reference
            if (isset($data['external_reference'])) {
                $requestData['externalReference'] = $data['external_reference'];
            }

            Log::info('ASAAS Checkout Request Data:', $requestData);

            $response = $this->makeRequest('POST', '/checkouts', $requestData);
            
            if (!$response['successful'] || !isset($response['json']['id'])) {
                throw new Exception('Invalid response from ASAAS API: ' . $response['body']);
            }

            $result = $response['json'];


            return [
                'success' => true,
                'checkout_id' => $result['id'],
                'checkout_url' => $result['link'],
                'expires_at' => $result['expiresAt'] ?? null,
                'gateway_response' => $result,
            ];

        } catch (Exception $e) {
            Log::error('ASAAS Checkout General Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ];
        }
    }

    /**
     * Create PIX Checkout
     */
    public function createPixCheckout(array $data): array
    {
        $checkoutData = [
            'billing_types' => ['PIX'],
            'charge_types' => ['DETACHED'],
            'minutes_to_expire' => $data['minutes_to_expire'] ?? 60,
            'items' => $data['items'],
            'callback' => $data['callback'] ?? null,
            'customer_data' => $data['customer_data'] ?? null,
            'external_reference' => $data['external_reference'] ?? null,
        ];

        return $this->createCheckout($checkoutData);
    }

    /**
     * Create Credit Card Checkout
     */
    public function createCreditCardCheckout(array $data): array
    {
        $checkoutData = [
            'billing_types' => ['CREDIT_CARD'],
            'charge_types' => $data['installments'] > 1 ? ['INSTALLMENT'] : ['DETACHED'],
            'minutes_to_expire' => $data['minutes_to_expire'] ?? 60,
            'items' => $data['items'],
            'callback' => $data['callback'] ?? null,
            'customer_data' => $data['customer_data'] ?? null,
            'external_reference' => $data['external_reference'] ?? null,
        ];

        // Add installment configuration if provided
        if (isset($data['max_installments']) && $data['max_installments'] > 1) {
            $checkoutData['installment'] = [
                'maxInstallmentCount' => $data['max_installments']
            ];
        }

        return $this->createCheckout($checkoutData);
    }

    /**
     * Create Multi-Payment Checkout (PIX + Credit Card)
     */
    public function createMultiPaymentCheckout(array $data): array
    {
        $checkoutData = [
            'billing_types' => ['PIX', 'CREDIT_CARD'],
            'charge_types' => ['DETACHED'],
            'minutes_to_expire' => $data['minutes_to_expire'] ?? 60,
            'items' => $data['items'],
            'callback' => $data['callback'] ?? null,
            'customer_data' => $data['customer_data'] ?? null,
            'external_reference' => $data['external_reference'] ?? null,
        ];

        // Add installment support if requested
        if (isset($data['max_installments']) && $data['max_installments'] > 1) {
            $checkoutData['charge_types'][] = 'INSTALLMENT';
            $checkoutData['installment'] = [
                'maxInstallmentCount' => $data['max_installments']
            ];
        }

        return $this->createCheckout($checkoutData);
    }

    public function getSupportedMethods(): array
    {
        return ['pix', 'credit_card', 'bank_slip'];
    }

    public function validateWebhookSignature(array $headers, string $body): bool
    {
        // ASAAS webhook validation logic
        // For now, return true - implement proper validation based on ASAAS docs
        return true;
    }

    public function getGatewayName(): string
    {
        return 'asaas';
    }

    private function getOrCreateCustomer(array $payerData): array
    {
        try {
            // Validate required fields
            if (empty($payerData['email'])) {
                throw new Exception('Email is required for customer creation');
            }
            
            $email = $payerData['email'];
            
            // Try to find existing customer by email
            $result = $this->makeRequest('GET', '/customers', ['email' => $email]);
            
            // Check if customer exists and return it
            if ($result['successful'] && !empty($result['json']['data']) && is_array($result['json']['data'])) {
                $customer = $result['json']['data'][0];
                return $this->ensureValidCustomer($customer, $payerData);
            }

            // Create new customer
            $customerData = [
                'name' => $payerData['name'] ?? 'Cliente',
                'email' => $email,
            ];

            // Only add document if it's not empty and valid
            if (isset($payerData['document']) && !empty(trim($payerData['document']))) {
                $document = preg_replace('/[^0-9]/', '', $payerData['document']);
                if (strlen($document) >= 11) { // CPF has 11 digits, CNPJ has 14
                    $customerData['cpfCnpj'] = $document;
                }
            }

            // Only add phone if it's not empty
            if (isset($payerData['phone']) && !empty(trim($payerData['phone']))) {
                $phone = preg_replace('/[^0-9]/', '', $payerData['phone']);
                if (strlen($phone) >= 10) { // Minimum phone number length
                    $customerData['phone'] = $phone;
                }
            }

            if (isset($payerData['address'])) {
                if (isset($payerData['address']['zip_code'])) {
                    $customerData['postalCode'] = $payerData['address']['zip_code'];
                }
                if (isset($payerData['address']['number'])) {
                    $customerData['addressNumber'] = $payerData['address']['number'];
                }
                if (isset($payerData['address']['complement'])) {
                    $customerData['complement'] = $payerData['address']['complement'];
                }
                if (isset($payerData['address']['city'])) {
                    $customerData['city'] = $payerData['address']['city'];
                }
                if (isset($payerData['address']['state'])) {
                    $customerData['state'] = $payerData['address']['state'];
                }
            }

            Log::info('ASAAS Creating new customer:', $customerData);

            $result = $this->makeRequest('POST', '/customers', $customerData);
            
            Log::info('ASAAS Customer Creation Response:', [
                'status_code' => $result['status_code'],
                'successful' => $result['successful'],
                'response' => $result['json'],
                'has_id' => isset($result['json']['id']),
                'response_body' => $result['body']
            ]);
            
            // Validate that the new customer has an ID
            if (!$result['successful'] || !isset($result['json']['id']) || empty($result['json']['id'])) {
                Log::error('ASAAS Customer creation failed - invalid response:', [
                    'response' => $result['json'],
                    'raw_response' => $result['body'],
                    'customer_data' => $customerData
                ]);
                
                // Return fallback customer instead of throwing exception
                return $this->createFallbackCustomer($payerData);
            }

            Log::info('ASAAS Customer created successfully:', [
                'customer_id' => $result['json']['id'],
                'customer_name' => $result['json']['name'] ?? 'N/A'
            ]);

            return $this->ensureValidCustomer($result['json'], $payerData);

        } catch (Exception $e) {
            Log::error('ASAAS Customer General Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payer_data' => $payerData,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Return a fallback customer structure
            return $this->createFallbackCustomer($payerData);
        }
    }

    /**
     * Ensure we always return a valid customer array
     */
    private function ensureValidCustomer($customer, array $payerData): array
    {
        if (!is_array($customer) || !isset($customer['id']) || empty($customer['id'])) {
            Log::warning('Invalid customer data received, using fallback:', [
                'customer' => $customer,
                'payer_data' => $payerData
            ]);
            return $this->createFallbackCustomer($payerData);
        }
        
        return $customer;
    }

    /**
     * Create a fallback customer structure when API fails
     */
    private function createFallbackCustomer(array $payerData): array
    {
        return [
            'id' => 'fallback_' . uniqid() . '_' . time(),
            'name' => $payerData['name'] ?? 'Cliente Temporário',
            'email' => $payerData['email'] ?? 'cliente@temp.com',
            'cpfCnpj' => $payerData['document'] ?? '',
            'phone' => $payerData['phone'] ?? '',
            'object' => 'customer',
            'dateCreated' => now()->toISOString(),
            'notificationDisabled' => false,
            'observations' => 'Cliente criado como fallback devido a erro na API'
        ];
    }

    private function generatePixQrCode(string $paymentId): array
    {
        try {
            $response = $this->makeRequest('GET', "/payments/{$paymentId}/pixQrCode");
            
            if (!$response['successful']) {
                Log::warning('ASAAS PIX QR Code Error:', [
                    'payment_id' => $paymentId,
                    'response' => $response['body']
                ]);
                return [
                    'qr_code' => null,
                    'qr_code_base64' => null,
                ];
            }
            
            $result = $response['json'];

            return [
                'qr_code' => $result['payload'] ?? null,
                'qr_code_base64' => $result['encodedImage'] ?? null,
            ];

        } catch (Exception $e) {
            Log::warning('ASAAS PIX QR Code Error:', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);

            return [
                'qr_code' => null,
                'qr_code_base64' => null,
            ];
        }
    }

    /**
     * Get Checkout Status
     */
    public function getCheckoutStatus(string $checkoutId): array
    {
        try {
            $response = $this->makeRequest('GET', "/checkouts/{$checkoutId}");
            
            if (!$response['successful']) {
                throw new Exception('Invalid response from ASAAS API: ' . $response['body']);
            }
            
            $result = $response['json'];

            return [
                'success' => true,
                'status' => $result['status'],
                'payment_id' => $result['paymentId'] ?? null,
                'gateway_response' => $result,
            ];

        } catch (Exception $e) {
            Log::error('ASAAS Get Checkout Status General Error:', [
                'checkout_id' => $checkoutId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ];
        }
    }

    /**
     * Cancel Checkout
     */
    public function cancelCheckout(string $checkoutId): array
    {
        try {
            $response = $this->makeRequest('DELETE', "/checkouts/{$checkoutId}");
            
            if (!$response['successful']) {
                throw new Exception('Invalid response from ASAAS API: ' . $response['body']);
            }
            
            $result = $response['json'];

            return [
                'success' => true,
                'message' => 'Checkout cancelado com sucesso',
                'gateway_response' => $result,
            ];

        } catch (Exception $e) {
            Log::error('ASAAS Cancel Checkout General Error:', [
                'checkout_id' => $checkoutId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ];
        }
    }

    /**
     * Helper method to build items array for checkout
     */
    public function buildCheckoutItems(array $items): array
    {
        $checkoutItems = [];
        
        foreach ($items as $item) {
            $checkoutItems[] = [
                'name' => $item['name'],
                'description' => $item['description'] ?? $item['name'],
                'quantity' => $item['quantity'] ?? 1,
                'value' => (float) $item['value'],
                'imageBase64' => $item['image_base64'] ?? null,
            ];
        }

        return $checkoutItems;
    }

    /**
     * Helper method to build customer data for checkout
     */
    public function buildCustomerData(array $customer): array
    {
        return [
            'name' => $customer['name'],
            'cpfCnpj' => $customer['cpf'] ?? $customer['cnpj'] ?? $customer['document'],
            'email' => $customer['email'],
            'phone' => $customer['phone'] ?? null,
            'address' => $customer['address'] ?? null,
            'addressNumber' => $customer['address_number'] ?? null,
            'complement' => $customer['complement'] ?? null,
            'postalCode' => $customer['postal_code'] ?? $customer['zipcode'] ?? null,
            'province' => $customer['province'] ?? $customer['district'] ?? null,
            'city' => $customer['city_code'] ?? null,
        ];
    }

    private function mapStatus(string $status): string
    {
        return match($status) {
            'PENDING' => 'pending',
            'RECEIVED' => 'approved',
            'CONFIRMED' => 'approved',
            'OVERDUE' => 'cancelled',
            'RECEIVED_IN_CASH' => 'approved',
            'REFUNDED' => 'refunded',
            'AWAITING_RISK_ANALYSIS' => 'processing',
            default => 'pending'
        };
    }
}