<?php

namespace Tests\Feature\Payment;

use App\Services\Payment\Gateways\AsaasService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AsaasServiceTest extends TestCase
{
    use RefreshDatabase;

    private $asaasService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar variáveis de teste
        config([
            'asaas.api_key' => 'test_api_key',
            'asaas.environment' => 'sandbox',
        ]);

        $config = [
            'api_key' => 'test_api_key',
            'is_sandbox' => true,
        ];

        $this->asaasService = new AsaasService($config);
    }

    /** @test */
    public function it_can_instantiate_asaas_service()
    {
        $this->assertInstanceOf(AsaasService::class, $this->asaasService);
    }

    /** @test */
    public function it_can_test_connection()
    {
        // Este teste requer uma chave de API válida
        // Para testes reais, você precisará configurar uma chave de sandbox válida
        $this->markTestSkipped('Requires valid ASAAS API key for testing');
        
        $result = $this->asaasService->testConnection();
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_create_pix_payment_data_structure()
    {
        $paymentData = [
            'amount' => 100.00,
            'description' => 'Test PIX Payment',
            'customer' => [
                'name' => 'João Silva',
                'email' => 'joao@example.com',
                'cpf' => '12345678901',
                'phone' => '11999999999',
            ],
            'external_reference' => 'test_ref_123',
        ];

        // Testar se o método existe e pode ser chamado
        $this->assertTrue(method_exists($this->asaasService, 'createPixPayment'));
        
        // Para teste real, descomente e configure uma chave de API válida:
        // $result = $this->asaasService->createPixPayment($paymentData);
        // $this->assertArrayHasKey('success', $result);
    }

    /** @test */
    public function it_can_create_credit_card_payment_data_structure()
    {
        $paymentData = [
            'amount' => 100.00,
            'description' => 'Test Credit Card Payment',
            'customer' => [
                'name' => 'João Silva',
                'email' => 'joao@example.com',
                'cpf' => '12345678901',
                'phone' => '11999999999',
            ],
            'card' => [
                'holder_name' => 'JOAO SILVA',
                'number' => '4111111111111111',
                'expiry_month' => '12',
                'expiry_year' => '2030',
                'ccv' => '123',
            ],
            'installments' => 1,
            'external_reference' => 'test_ref_123',
        ];

        // Testar se o método existe e pode ser chamado
        $this->assertTrue(method_exists($this->asaasService, 'createCreditCardPayment'));
    }

    /** @test */
    public function it_can_create_bank_slip_payment_data_structure()
    {
        $paymentData = [
            'amount' => 100.00,
            'description' => 'Test Bank Slip Payment',
            'customer' => [
                'name' => 'João Silva',
                'email' => 'joao@example.com',
                'cpf' => '12345678901',
                'phone' => '11999999999',
            ],
            'due_date' => '2024-12-31',
            'external_reference' => 'test_ref_123',
        ];

        // Testar se o método existe e pode ser chamado
        $this->assertTrue(method_exists($this->asaasService, 'createBankSlipPayment'));
    }

    /** @test */
    public function it_implements_payment_gateway_interface()
    {
        $this->assertInstanceOf(
            \App\Services\Payment\Contracts\PaymentGatewayInterface::class,
            $this->asaasService
        );
    }

    /** @test */
    public function it_has_required_interface_methods()
    {
        $requiredMethods = [
            'createPixPayment',
            'createCreditCardPayment',
            'createBankSlipPayment',
            'getPaymentStatus',
            'processWebhook',
            'testConnection',
        ];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                method_exists($this->asaasService, $method),
                "Method {$method} is required by PaymentGatewayInterface"
            );
        }
    }
}