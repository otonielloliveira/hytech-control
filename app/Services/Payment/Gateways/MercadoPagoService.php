<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
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
        try {
            $requestData = [
                'transaction_amount' => (float) $data['amount'],
                'currency_id' => $data['currency'] ?? 'BRL',
                'description' => $data['description'] ?? 'Pagamento',
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => $data['payer']['email'],
                    'first_name' => $data['payer']['name'] ?? '',
                    'identification' => [
                        'type' => 'CPF',
                        'number' => $data['payer']['document'] ?? ''
                    ]
                ]
            ];
            
            if (isset($data['external_reference'])) {
                $requestData['external_reference'] = $data['external_reference'];
            }
            
            if (isset($data['metadata'])) {
                $requestData['metadata'] = $data['metadata'];
            }
            
            $payment = $this->paymentClient->create($requestData);
            
            return [
                'success' => true,
                'transaction_id' => $payment->id,
                'status' => $this->mapStatus($payment->status),
                'pix_code' => $payment->point_of_interaction->transaction_data->qr_code ?? null,
                'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64 ?? null,
                'expires_at' => $payment->date_of_expiration ?? null,
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

    public function createCreditCardPayment(array $data): array
    {
        try {
            $requestData = [
                'transaction_amount' => (float) $data['amount'],
                'currency_id' => $data['currency'] ?? 'BRL',
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
                'currency_id' => $data['currency'] ?? 'BRL',
                'description' => $data['description'] ?? 'Pagamento',
                'payment_method_id' => $data['payment_method_id'] ?? 'bolbradesco',
                'payer' => [
                    'email' => $data['payer']['email'],
                    'first_name' => $data['payer']['name'] ?? '',
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