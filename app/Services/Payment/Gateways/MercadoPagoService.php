<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Order\OrderClient;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Exceptions\MPApiException;
use Exception;

class MercadoPagoService implements PaymentGatewayInterface
{
    private $accessToken;
    private $isSandbox;
    private $paymentClient;

    public function __construct($config)
    {
        $this->accessToken = $config['access_token'] ?? null;
        $this->isSandbox = $config['is_sandbox'] ?? true;
        
        if ($this->accessToken) {
            MercadoPagoConfig::setAccessToken($this->accessToken);
            MercadoPagoConfig::setRuntimeEnviroment($this->isSandbox ? MercadoPagoConfig::LOCAL : MercadoPagoConfig::SERVER);
            $this->paymentClient = new PaymentClient();
        }
    }

    public function createPixPayment(array $data): array
    {
        // Try with Order API first (newer approach)
        $orderResult = $this->createPixPaymentWithOrderAPI($data);
        if ($orderResult['success']) {
            return $orderResult;
        }
        
        // Fallback to Payment API
        return $this->createPixPaymentWithPaymentAPI($data);
    }
    
    private function createPixPaymentWithOrderAPI(array $data): array
    {
        try {
            $orderClient = new \MercadoPago\Client\Order\OrderClient();
            
            $requestData = [
                'type' => 'online',
                'processing_mode' => 'automatic',
                'total_amount' => (string) $data['amount'],
                'external_reference' => $data['external_reference'] ?? 'ref_' . time(),
                'transactions' => [
                    'payments' => [
                        [
                            'amount' => (string) $data['amount'],
                            'payment_method' => [
                                'id' => 'pix',
                                'type' => 'bank_transfer'
                            ]
                        ]
                    ]
                ],
                'payer' => [
                    'email' => $data['payer']['email']
                ]
            ];
            
            // Add optional payer fields
            if (isset($data['payer']['name']) && !empty($data['payer']['name'])) {
                $requestData['payer']['first_name'] = $data['payer']['name'];
            }
            
            if (isset($data['payer']['document']) && !empty($data['payer']['document'])) {
                $requestData['payer']['identification'] = [
                    'type' => 'CPF',
                    'number' => $data['payer']['document']
                ];
            }
            
            if (isset($data['description'])) {
                $requestData['description'] = $data['description'];
            }

            \Log::info('MercadoPago Order API PIX Request:', $requestData);
            
            // Add idempotency key
            $requestOptions = new \MercadoPago\Client\Common\RequestOptions();
            $requestOptions->setCustomHeaders(['X-Idempotency-Key: ' . uniqid('pix_', true)]);

            $order = $orderClient->create($requestData, $requestOptions);
            
            $payment = $order->transactions->payments[0] ?? null;
            if (!$payment) {
                throw new Exception('No payment found in order response');
            }
            
            return [
                'success' => true,
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'status' => $payment->status ?? $order->status,
                'qr_code' => $payment->payment_method->qr_code ?? null,
                'qr_code_base64' => null, // Will be in point_of_interaction if available
                'ticket_url' => $payment->payment_method->ticket_url ?? null,
                'data' => $order,
                'api_used' => 'order_api'
            ];
            
        } catch (Exception $e) {
            \Log::warning('MercadoPago Order API failed, will try Payment API:', [
                'error' => $e->getMessage(),
                'status_code' => method_exists($e, 'getStatusCode') ? $e->getStatusCode() : null
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'api_used' => 'order_api_failed'
            ];
        }
    }
    
    private function createPixPaymentWithPaymentAPI(array $data): array
    {
        try {
            // Based on official PaymentClient examples
            $requestData = [
                'transaction_amount' => (float) $data['amount'],
                'description' => $data['description'] ?? 'Pagamento PIX',
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => $data['payer']['email']
                ]
            ];
            
            // Add optional fields only if provided
            if (isset($data['payer']['name']) && !empty($data['payer']['name'])) {
                $requestData['payer']['first_name'] = $data['payer']['name'];
            }
            
            if (isset($data['payer']['document']) && !empty($data['payer']['document'])) {
                $requestData['payer']['identification'] = [
                    'type' => 'CPF',
                    'number' => $data['payer']['document']
                ];
            }
            
            if (isset($data['external_reference'])) {
                $requestData['external_reference'] = $data['external_reference'];
            }
            
            if (isset($data['metadata'])) {
                $requestData['metadata'] = $data['metadata'];
            }

            \Log::info('MercadoPago Payment API PIX Request:', $requestData);
            
            // Add idempotency key
            $requestOptions = new \MercadoPago\Client\Common\RequestOptions();
            $requestOptions->setCustomHeaders(['X-Idempotency-Key: ' . uniqid('pix_', true)]);

            $payment = $this->paymentClient->create($requestData, $requestOptions);
            
            return [
                'success' => true,
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'qr_code' => $payment->point_of_interaction->transaction_data->qr_code ?? null,
                'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64 ?? null,
                'ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url ?? null,
                'data' => $payment,
                'api_used' => 'payment_api'
            ];
            
        } catch (MPApiException $e) {
            \Log::error('MercadoPago Payment API PIX Error:', [
                'message' => $e->getMessage(),
                'api_response' => $e->getApiResponse(),
                'status_code' => $e->getStatusCode()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'api_response' => $e->getApiResponse(),
                'status_code' => $e->getStatusCode(),
                'api_used' => 'payment_api'
            ];
        } catch (Exception $e) {
            \Log::error('MercadoPago Payment API General Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'api_used' => 'payment_api'
            ];
        }
    }    public function createCreditCardPayment(array $data): array
    {
        try {
            $requestData = [
                'transaction_amount' => (float) $data['amount'],
                'description' => $data['description'] ?? 'Pagamento',
                'installments' => (int) ($data['installments'] ?? 1),
                'payment_method_id' => $data['payment_method_id'] ?? 'visa',
                'token' => $data['card_token'],
                'payer' => [
                    'email' => $data['payer']['email'],
                    'identification' => [
                        'type' => 'CPF',
                        'number' => $data['payer']['document'] ?? ''
                    ]
                ]
            ];
            
            if (isset($data['external_reference'])) {
                $requestData['external_reference'] = $data['external_reference'];
            }
            
            $payment = $this->paymentClient->create($requestData);
            
            return [
                'success' => true,
                'transaction_id' => $payment->id,
                'status' => $this->mapStatus($payment->status),
                'gateway_response' => (array) $payment,
            ];
            
        } catch (MPApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
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
                'transaction_amount' => (float) $data['amount'],
                'description' => $data['description'] ?? 'Pagamento',
                'payment_method_id' => 'bolbradesco',
                'payer' => [
                    'email' => $data['payer']['email'],
                    'first_name' => $data['payer']['first_name'] ?? '',
                    'last_name' => $data['payer']['last_name'] ?? '',
                    'identification' => [
                        'type' => 'CPF',
                        'number' => $data['payer']['document'] ?? ''
                    ]
                ]
            ];
            
            if (isset($data['external_reference'])) {
                $requestData['external_reference'] = $data['external_reference'];
            }
            
            $payment = $this->paymentClient->create($requestData);
            
            return [
                'success' => true,
                'transaction_id' => $payment->id,
                'status' => $this->mapStatus($payment->status),
                'bank_slip_url' => $payment->transaction_details->external_resource_url ?? null,
                'gateway_response' => (array) $payment,
            ];
            
        } catch (MPApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
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
            $payment = $this->paymentClient->get($transactionId);
            
            return [
                'success' => true,
                'status' => $this->mapStatus($payment->status),
                'gateway_response' => (array) $payment,
            ];
            
        } catch (MPApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
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
            $payment = $this->paymentClient->cancel($transactionId);
            
            return [
                'success' => true,
                'status' => $this->mapStatus($payment->status),
                'gateway_response' => (array) $payment,
            ];
            
        } catch (MPApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
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
            if (isset($data['data']['id'])) {
                $payment = $this->paymentClient->get($data['data']['id']);
                
                return [
                    'success' => true,
                    'transaction_id' => $payment->id,
                    'status' => $this->mapStatus($payment->status),
                    'external_reference' => $payment->external_reference,
                    'gateway_response' => (array) $payment,
                ];
            }
            
            return ['success' => false, 'error' => 'Invalid webhook data'];
            
        } catch (MPApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function testConnection(): array
    {
        try {
            if (!$this->accessToken) {
                return [
                    'success' => false,
                    'error' => 'Access token not configured'
                ];
            }

            // Test 1: Try to get payment methods (simpler operation)
            $methodsClient = new \MercadoPago\Client\PaymentMethod\PaymentMethodClient();
            $methods = $methodsClient->list();
            
            if (!$methods) {
                return [
                    'success' => false,
                    'error' => 'Failed to retrieve payment methods',
                    'test_performed' => 'payment_methods_list'
                ];
            }

            // Test 2: Try to create a simple PIX payment with minimal data
            $requestData = [
                'transaction_amount' => 1.00,
                'description' => 'Test payment',
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => 'test@test.com'
                ]
            ];

            $payment = $this->paymentClient->create($requestData);
            
            return [
                'success' => true,
                'message' => 'Connection successful',
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'test_performed' => 'create_pix_payment'
            ];
            
        } catch (MPApiException $e) {
            \Log::error('MercadoPago Connection Test Error:', [
                'message' => $e->getMessage(),
                'api_response' => $e->getApiResponse(),
                'status_code' => $e->getStatusCode()
            ]);
            
            // If PIX fails with internal error, try with different approach
            if ($e->getStatusCode() === 500) {
                return [
                    'success' => false,
                    'error' => 'MercadoPago API internal error - may be temporary service issue',
                    'details' => 'Status 500: ' . $e->getMessage(),
                    'recommendation' => 'Try again later or contact MercadoPago support',
                    'test_performed' => 'create_pix_payment'
                ];
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'api_response' => $e->getApiResponse(),
                'status_code' => $e->getStatusCode(),
                'test_performed' => 'create_pix_payment'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'test_performed' => 'connection_test'
            ];
        }
    }

    public function getSupportedMethods(): array
    {
        return ['pix', 'credit_card', 'bank_slip'];
    }

    public function validateWebhookSignature(array $headers, string $body): bool
    {
        // MercadoPago webhook validation logic
        // For now, return true - implement proper validation
        return true;
    }

    public function getGatewayName(): string
    {
        return 'mercadopago';
    }

    private function mapStatus(string $status): string
    {
        return match($status) {
            'pending' => 'pending',
            'approved' => 'approved',
            'authorized' => 'processing',
            'in_process' => 'processing',
            'in_mediation' => 'processing',
            'rejected' => 'rejected',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            'charged_back' => 'refunded',
            default => 'pending'
        };
    }
}