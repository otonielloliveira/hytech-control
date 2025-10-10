<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Efi\Exception\EfiException;
use Efi\EfiPay;
use Exception;

class EfiPayService implements PaymentGatewayInterface
{
    private $clientId;
    private $clientSecret;
    private $isSandbox;
    private $efi;

    public function __construct($config)
    {
        $this->clientId = $config['client_id'] ?? null;
        $this->clientSecret = $config['client_secret'] ?? null;
        $this->isSandbox = $config['is_sandbox'] ?? true;
        
        if ($this->clientId && $this->clientSecret) {
            $options = [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'sandbox' => $this->isSandbox,
            ];
            
            $this->efi = new EfiPay($options);
        }
    }

    public function createPixPayment(array $data): array
    {
        try {
            // Criar cobrança PIX
            $chargeData = [
                'calendario' => [
                    'expiracao' => 3600 // 1 hora
                ],
                'valor' => [
                    'original' => number_format($data['amount'], 2, '.', '')
                ],
                'chave' => $this->getPixKey(),
                'solicitacaoPagador' => $data['description'] ?? 'Pagamento'
            ];
            
            if (isset($data['payer']['name'])) {
                $chargeData['devedor'] = [
                    'nome' => $data['payer']['name'],
                    'cpf' => $this->formatCpf($data['payer']['document'] ?? '')
                ];
            }
            
            $charge = $this->efi->pixCreateImmediateCharge([], $chargeData);
            
            if (isset($charge['txid'])) {
                // Gerar QR Code
                $qrCodeData = [
                    'id' => $charge['loc']['id']
                ];
                
                $qrCode = $this->efi->pixGenerateQRCode($qrCodeData);
                
                return [
                    'success' => true,
                    'transaction_id' => $charge['txid'],
                    'status' => 'pending',
                    'pix_code' => $qrCode['qrcode'] ?? null,
                    'qr_code_url' => $qrCode['imagemQrcode'] ?? null,
                    'expires_at' => now()->addHour(),
                    'gateway_response' => $charge,
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Erro ao criar cobrança PIX',
            ];
            
        } catch (EfiException $e) {
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
            $paymentData = [
                'payment' => [
                    'credit_card' => [
                        'installments' => (int) ($data['installments'] ?? 1),
                        'payment_token' => $data['card_token'],
                        'billing_address' => [
                            'street' => $data['billing']['street'] ?? '',
                            'number' => $data['billing']['number'] ?? '',
                            'neighborhood' => $data['billing']['neighborhood'] ?? '',
                            'zipcode' => $data['billing']['zipcode'] ?? '',
                            'city' => $data['billing']['city'] ?? '',
                            'state' => $data['billing']['state'] ?? '',
                        ]
                    ]
                ]
            ];
            
            $items = [[
                'name' => $data['description'] ?? 'Produto',
                'amount' => 1,
                'value' => (int) ($data['amount'] * 100) // centavos
            ]];
            
            $customer = [
                'name' => $data['payer']['name'] ?? '',
                'email' => $data['payer']['email'],
                'cpf' => $data['payer']['document'] ?? '',
                'phone_number' => $data['payer']['phone'] ?? '',
            ];
            
            $body = [
                'items' => $items,
                'customer' => $customer,
                'payment' => $paymentData['payment']
            ];
            
            $response = $this->efi->createOneStepCharge([], $body);
            
            return [
                'success' => true,
                'transaction_id' => $response['data']['charge_id'],
                'status' => $this->mapStatus($response['data']['status']),
                'gateway_response' => $response,
            ];
            
        } catch (EfiException $e) {
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
            $items = [[
                'name' => $data['description'] ?? 'Produto',
                'amount' => 1,
                'value' => (int) ($data['amount'] * 100) // centavos
            ]];
            
            $customer = [
                'name' => $data['payer']['name'] ?? '',
                'email' => $data['payer']['email'],
                'cpf' => $data['payer']['document'] ?? '',
                'phone_number' => $data['payer']['phone'] ?? '',
            ];
            
            $body = [
                'items' => $items,
                'customer' => $customer,
                'payment' => [
                    'banking_billet' => [
                        'expire_at' => now()->addDays(3)->format('Y-m-d'),
                        'customer' => $customer
                    ]
                ]
            ];
            
            $response = $this->efi->createOneStepCharge([], $body);
            
            return [
                'success' => true,
                'transaction_id' => $response['data']['charge_id'],
                'status' => $this->mapStatus($response['data']['status']),
                'bank_slip_url' => $response['data']['payment']['banking_billet']['link'] ?? null,
                'gateway_response' => $response,
            ];
            
        } catch (EfiException $e) {
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
            $params = ['txid' => $transactionId];
            $response = $this->efi->pixDetailCharge($params);
            
            return [
                'success' => true,
                'status' => $this->mapStatus($response['status']),
                'gateway_response' => $response,
            ];
            
        } catch (EfiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
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
        // EFI Pay não permite cancelamento de PIX
        return [
            'success' => false,
            'error' => 'Cancelamento não disponível para PIX',
        ];
    }

    public function processWebhook(array $data): array
    {
        try {
            if (isset($data['pix'])) {
                foreach ($data['pix'] as $pixData) {
                    $txid = $pixData['txid'];
                    $params = ['txid' => $txid];
                    $response = $this->efi->pixDetailCharge($params);
                    
                    return [
                        'success' => true,
                        'transaction_id' => $txid,
                        'status' => $this->mapStatus($response['status']),
                        'gateway_response' => $response,
                    ];
                }
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
        // EFI Pay webhook validation logic
        // For now, return true - implement proper validation
        return true;
    }

    public function getGatewayName(): string
    {
        return 'efipay';
    }

    private function getPixKey(): string
    {
        // Retorna a chave PIX configurada
        return config('services.pix.key', 'seu@email.com');
    }

    private function formatCpf(string $cpf): string
    {
        return preg_replace('/\D/', '', $cpf);
    }

    private function mapStatus(string $status): string
    {
        return match($status) {
            'ATIVA' => 'pending',
            'CONCLUIDA' => 'approved',
            'REMOVIDA_PELO_USUARIO_RECEBEDOR' => 'cancelled',
            'REMOVIDA_PELO_PSP' => 'cancelled',
            'waiting_payment' => 'pending',
            'paid' => 'approved',
            'unpaid' => 'rejected',
            'refunded' => 'refunded',
            'contested' => 'processing',
            default => 'pending'
        };
    }
}