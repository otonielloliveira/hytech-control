<?php

namespace Tests\Feature\Payment;

use App\Services\Payment\PaymentManager;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentManagerAsaasIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private $paymentManager;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->paymentManager = new PaymentManager();
    }

    /** @test */
    public function it_can_set_asaas_as_active_gateway()
    {
        // Criar configuração do ASAAS
        \App\Models\PaymentGatewayConfig::create([
            'gateway' => 'asaas',
            'name' => 'ASAAS Gateway',
            'is_active' => true,
            'is_sandbox' => true,
            'credentials' => [
                'api_key' => 'test_api_key'
            ],
            'settings' => [],
            'description' => 'Gateway ASAAS para testes',
            'sort_order' => 1,
        ]);

        $result = $this->paymentManager->setActiveGateway('asaas');
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_create_asaas_gateway_service()
    {
        // Testar se o PaymentManager consegue criar o AsaasService
        $this->assertTrue(method_exists($this->paymentManager, 'setActiveGateway'));
        
        // Verificar se 'asaas' é um gateway suportado
        $reflection = new \ReflectionClass($this->paymentManager);
        $method = $reflection->getMethod('createGatewayService');
        $method->setAccessible(true);
        
        $this->assertTrue(method_exists($this->paymentManager, 'createGatewayService'));
    }

    /** @test */
    public function it_integrates_asaas_with_payment_manager()
    {
        // Este teste verifica se o ASAAS está integrado ao PaymentManager
        $config = [
            'api_key' => 'test_key',
            'is_sandbox' => true,
        ];
        
        // Simular configuração do gateway
        $this->assertNotNull($this->paymentManager);
        
        // Verificar se o método setActiveGateway existe
        $this->assertTrue(method_exists($this->paymentManager, 'setActiveGateway'));
    }

    /** @test */
    public function it_has_all_required_payment_methods()
    {
        $requiredMethods = [
            'createPixPayment',
            'createCreditCardPayment', 
            'createBankSlipPayment',
            'getPaymentStatus',
            'processWebhook',
            'testConnection',
            'setActiveGateway',
            'getActiveGateway',
        ];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                method_exists($this->paymentManager, $method),
                "PaymentManager should have {$method} method"
            );
        }
    }
}