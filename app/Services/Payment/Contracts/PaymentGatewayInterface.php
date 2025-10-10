<?php

namespace App\Services\Payment\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Create a PIX payment
     */
    public function createPixPayment(array $data): array;

    /**
     * Create a credit card payment
     */
    public function createCreditCardPayment(array $data): array;

    /**
     * Create a bank slip payment
     */
    public function createBankSlipPayment(array $data): array;

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $transactionId): array;

    /**
     * Cancel a payment
     */
    public function cancelPayment(string $transactionId): array;

    /**
     * Process webhook
     */
    public function processWebhook(array $data): array;

    /**
     * Get supported payment methods
     */
    public function getSupportedMethods(): array;

    /**
     * Validate webhook signature
     */
    public function validateWebhookSignature(array $headers, string $body): bool;

    /**
     * Get gateway name
     */
    public function getGatewayName(): string;
}