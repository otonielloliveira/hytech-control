<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use App\Models\PaymentGatewayConfig;

interface PaymentGatewayInterface
{
    /**
     * Constructor
     */
    public function __construct(PaymentGatewayConfig $config);

    /**
     * Get gateway configuration
     */
    public function getConfig(): PaymentGatewayConfig;

    /**
     * Create PIX payment
     */
    public function createPixPayment(Payment $payment, array $data = []): array;

    /**
     * Create credit card payment
     */
    public function createCreditCardPayment(Payment $payment, array $cardData): array;

    /**
     * Create bank slip payment
     */
    public function createBankSlipPayment(Payment $payment, array $data = []): array;

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Payment $payment): array;

    /**
     * Process webhook notification
     */
    public function processWebhook(array $data): bool;

    /**
     * Validate gateway configuration
     */
    public function validateConfig(): bool;

    /**
     * Test connection with the gateway
     */
    public function testConnection(): array;
}