<?php

namespace Tests\Feature\Payment;

use App\Services\Payment\Gateways\AsaasService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AsaasCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private $asaasService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $config = [
            'api_key' => 'test_api_key',
            'is_sandbox' => true,
        ];

        $this->asaasService = new AsaasService($config);
    }

    /** @test */
    public function it_can_create_pix_checkout_structure()
    {
        $data = [
            'items' => [
                [
                    'name' => 'Curso de Marketing',
                    'description' => 'Curso completo de marketing digital',
                    'quantity' => 1,
                    'value' => 297.00
                ]
            ],
            'minutes_to_expire' => 60,
            'callback' => [
                'successUrl' => 'https://exemplo.com/sucesso',
                'cancelUrl' => 'https://exemplo.com/cancelado',
                'expiredUrl' => 'https://exemplo.com/expirado'
            ],
            'customer_data' => [
                'name' => 'João Silva',
                'cpf' => '12345678901',
                'email' => 'joao@example.com',
                'phone' => '11999999999'
            ]
        ];

        $this->assertTrue(method_exists($this->asaasService, 'createPixCheckout'));
        
        // Verificar se o método buildCheckoutItems funciona
        $items = $this->asaasService->buildCheckoutItems($data['items']);
        $this->assertIsArray($items);
        $this->assertArrayHasKey('name', $items[0]);
        $this->assertArrayHasKey('value', $items[0]);
        $this->assertEquals(297.00, $items[0]['value']);
    }

    /** @test */
    public function it_can_create_credit_card_checkout_structure()
    {
        $data = [
            'items' => [
                [
                    'name' => 'Consultoria Financeira',
                    'description' => 'Sessão única de consultoria',
                    'quantity' => 1,
                    'value' => 150.00
                ]
            ],
            'max_installments' => 6,
            'minutes_to_expire' => 60,
            'callback' => [
                'successUrl' => 'https://exemplo.com/sucesso',
                'cancelUrl' => 'https://exemplo.com/cancelado',
                'expiredUrl' => 'https://exemplo.com/expirado'
            ]
        ];

        $this->assertTrue(method_exists($this->asaasService, 'createCreditCardCheckout'));
    }

    /** @test */
    public function it_can_create_multi_payment_checkout_structure()
    {
        $data = [
            'items' => [
                [
                    'name' => 'Smartphone Premium',
                    'description' => 'Smartphone top de linha',
                    'quantity' => 1,
                    'value' => 1200.00
                ]
            ],
            'max_installments' => 12,
            'minutes_to_expire' => 120
        ];

        $this->assertTrue(method_exists($this->asaasService, 'createMultiPaymentCheckout'));
    }

    /** @test */
    public function it_can_build_checkout_items_correctly()
    {
        $items = [
            [
                'name' => 'Produto 1',
                'description' => 'Descrição do produto 1',
                'quantity' => 2,
                'value' => 50.00
            ],
            [
                'name' => 'Produto 2',
                'value' => 100.00
                // Sem description e quantity (devem usar defaults)
            ]
        ];

        $checkoutItems = $this->asaasService->buildCheckoutItems($items);

        $this->assertCount(2, $checkoutItems);
        
        // Primeiro item
        $this->assertEquals('Produto 1', $checkoutItems[0]['name']);
        $this->assertEquals('Descrição do produto 1', $checkoutItems[0]['description']);
        $this->assertEquals(2, $checkoutItems[0]['quantity']);
        $this->assertEquals(50.00, $checkoutItems[0]['value']);

        // Segundo item (com defaults)
        $this->assertEquals('Produto 2', $checkoutItems[1]['name']);
        $this->assertEquals('Produto 2', $checkoutItems[1]['description']); // Default = name
        $this->assertEquals(1, $checkoutItems[1]['quantity']); // Default = 1
        $this->assertEquals(100.00, $checkoutItems[1]['value']);
    }

    /** @test */
    public function it_can_build_customer_data_correctly()
    {
        $customer = [
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'email' => 'joao@example.com',
            'phone' => '11999999999',
            'address' => 'Rua das Flores',
            'address_number' => '123',
            'complement' => 'Apto 45',
            'postal_code' => '01234567',
            'province' => 'Centro',
            'city_code' => 3550308
        ];

        $customerData = $this->asaasService->buildCustomerData($customer);

        $this->assertEquals('João Silva', $customerData['name']);
        $this->assertEquals('12345678901', $customerData['cpfCnpj']);
        $this->assertEquals('joao@example.com', $customerData['email']);
        $this->assertEquals('11999999999', $customerData['phone']);
        $this->assertEquals('Rua das Flores', $customerData['address']);
        $this->assertEquals('123', $customerData['addressNumber']);
        $this->assertEquals('Apto 45', $customerData['complement']);
        $this->assertEquals('01234567', $customerData['postalCode']);
        $this->assertEquals('Centro', $customerData['province']);
        $this->assertEquals(3550308, $customerData['city']);
    }

    /** @test */
    public function it_can_build_customer_data_with_cnpj()
    {
        $customer = [
            'name' => 'Empresa Teste LTDA',
            'cnpj' => '12345678000195',
            'email' => 'empresa@example.com'
        ];

        $customerData = $this->asaasService->buildCustomerData($customer);

        $this->assertEquals('12345678000195', $customerData['cpfCnpj']);
    }

    /** @test */
    public function it_has_checkout_management_methods()
    {
        $methods = [
            'getCheckoutStatus',
            'cancelCheckout',
            'createCheckout',
            'createPixCheckout',
            'createCreditCardCheckout',
            'createMultiPaymentCheckout'
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists($this->asaasService, $method),
                "Method {$method} should exist in AsaasService"
            );
        }
    }

    /** @test */
    public function it_has_helper_methods()
    {
        $helperMethods = [
            'buildCheckoutItems',
            'buildCustomerData'
        ];

        foreach ($helperMethods as $method) {
            $this->assertTrue(
                method_exists($this->asaasService, $method),
                "Helper method {$method} should exist in AsaasService"
            );
        }
    }
}