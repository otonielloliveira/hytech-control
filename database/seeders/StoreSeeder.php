<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Models\ShippingRule;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar métodos de pagamento padrão
        $paymentMethods = [
            [
                'name' => 'PIX',
                'slug' => 'pix',
                'description' => 'Pagamento instantâneo via PIX',
                'gateway' => 'pix',
                'config' => [
                    'pix_key' => 'contato@forobrasileiro.com.br',
                    'beneficiary_name' => 'Foro Brasileiro',
                ],
                'is_active' => true,
                'sort_order' => 1,
                'fee_percentage' => 0,
                'fee_fixed' => 0,
                'supported_currencies' => ['BRL'],
            ],
            [
                'name' => 'Boleto Bancário',
                'slug' => 'boleto',
                'description' => 'Pagamento via boleto bancário',
                'gateway' => 'boleto',
                'config' => [
                    'bank' => '001',
                    'agency' => '1234-5',
                    'account' => '12345-6',
                    'instructions' => 'Pagamento via boleto bancário',
                ],
                'is_active' => true,
                'sort_order' => 2,
                'fee_percentage' => 0,
                'fee_fixed' => 2.50,
                'supported_currencies' => ['BRL'],
            ],
            [
                'name' => 'Cartão de Crédito',
                'slug' => 'credit-card',
                'description' => 'Pagamento com cartão de crédito',
                'gateway' => 'card',
                'config' => [
                    'processor' => 'mercadopago',
                    'installments' => true,
                    'max_installments' => 12,
                ],
                'is_active' => true,
                'sort_order' => 3,
                'fee_percentage' => 3.99,
                'fee_fixed' => 0,
                'supported_currencies' => ['BRL'],
            ],
            [
                'name' => 'Mercado Pago',
                'slug' => 'mercadopago',
                'description' => 'Pagamento via Mercado Pago',
                'gateway' => 'mercadopago',
                'config' => [
                    'access_token' => 'TEST-1234567890',
                    'public_key' => 'TEST-public-key',
                    'environment' => 'sandbox',
                ],
                'is_active' => false,
                'sort_order' => 4,
                'fee_percentage' => 4.99,
                'fee_fixed' => 0.39,
                'supported_currencies' => ['BRL'],
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }

        // Criar regras de frete
        $shippingRules = [
            [
                'name' => 'Frete Fixo Nacional',
                'description' => 'Frete fixo para todo o Brasil',
                'type' => 'fixed',
                'base_cost' => 10.00,
                'is_active' => true,
                'sort_order' => 1,
                'estimated_days_min' => 5,
                'estimated_days_max' => 10,
            ],
            [
                'name' => 'Frete Grátis',
                'description' => 'Frete grátis para pedidos acima de R$ 100',
                'type' => 'price_based',
                'base_cost' => 0,
                'min_order_value' => 100.00,
                'is_active' => true,
                'sort_order' => 2,
                'estimated_days_min' => 5,
                'estimated_days_max' => 12,
            ],
            [
                'name' => 'Frete por Peso',
                'description' => 'Frete calculado por peso do produto',
                'type' => 'weight_based',
                'base_cost' => 8.00,
                'cost_per_kg' => 2.50,
                'max_weight' => 30.000,
                'is_active' => true,
                'sort_order' => 3,
                'estimated_days_min' => 3,
                'estimated_days_max' => 8,
            ],
        ];

        foreach ($shippingRules as $rule) {
            ShippingRule::create($rule);
        }

        // Criar produtos de exemplo
        $products = [
            [
                'name' => 'Camiseta Foro Brasileiro',
                'description' => '<p>Camiseta oficial do Foro Brasileiro, feita com algodão 100% de alta qualidade.</p><p>Características:</p><ul><li>Malha 30.1 penteada</li><li>Gola careca</li><li>Manga curta</li><li>Estampa em silk screen</li></ul>',
                'short_description' => 'Camiseta oficial do Foro Brasileiro em algodão 100%',
                'sku' => 'CAMISETA-FB-001',
                'price' => 49.90,
                'sale_price' => 39.90,
                'stock_quantity' => 50,
                'weight' => 0.200,
                'length' => 30.0,
                'width' => 20.0,
                'height' => 2.0,
                'featured' => true,
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'name' => 'Livro: História do Brasil',
                'description' => '<p>Um livro completo sobre a história do Brasil, desde o descobrimento até os dias atuais.</p><p>Especificações:</p><ul><li>450 páginas</li><li>Formato: 16x23cm</li><li>Capa dura</li><li>Ilustrações coloridas</li></ul>',
                'short_description' => 'Livro completo sobre a história do Brasil',
                'sku' => 'LIVRO-HB-001',
                'price' => 89.90,
                'stock_quantity' => 25,
                'weight' => 0.800,
                'length' => 23.0,
                'width' => 16.0,
                'height' => 3.5,
                'featured' => true,
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'name' => 'Caneca Personalizada',
                'description' => '<p>Caneca de porcelana com logo do Foro Brasileiro.</p><p>Características:</p><ul><li>Capacidade: 325ml</li><li>Material: Porcelana</li><li>Impressão de alta qualidade</li><li>Resistente a micro-ondas</li></ul>',
                'short_description' => 'Caneca de porcelana com logo do Foro Brasileiro',
                'sku' => 'CANECA-FB-001',
                'price' => 24.90,
                'stock_quantity' => 100,
                'weight' => 0.350,
                'length' => 12.0,
                'width' => 9.0,
                'height' => 10.5,
                'featured' => false,
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'name' => 'Curso Online: Direito Constitucional',
                'description' => '<p>Curso completo de Direito Constitucional com certificado.</p><p>Conteúdo:</p><ul><li>20 horas de vídeo aulas</li><li>Material de apoio em PDF</li><li>Exercícios práticos</li><li>Certificado de conclusão</li></ul>',
                'short_description' => 'Curso online de Direito Constitucional com certificado',
                'sku' => 'CURSO-DC-001',
                'price' => 199.90,
                'sale_price' => 149.90,
                'stock_quantity' => 0, // Produto digital não tem estoque físico
                'manage_stock' => false,
                'weight' => 0,
                'featured' => true,
                'status' => 'active',
                'sort_order' => 4,
            ],
            [
                'name' => 'Agenda 2025',
                'description' => '<p>Agenda personalizada para 2025 com datas importantes do calendário jurídico.</p>',
                'short_description' => 'Agenda personalizada 2025 com calendário jurídico',
                'sku' => 'AGENDA-2025-001',
                'price' => 34.90,
                'stock_quantity' => 75,
                'weight' => 0.450,
                'length' => 21.0,
                'width' => 14.0,
                'height' => 2.0,
                'featured' => false,
                'status' => 'draft', // Será lançada em breve
                'sort_order' => 5,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $this->command->info('Dados da loja criados com sucesso!');
        $this->command->info('- ' . count($paymentMethods) . ' métodos de pagamento');
        $this->command->info('- ' . count($shippingRules) . ' regras de frete');
        $this->command->info('- ' . count($products) . ' produtos de exemplo');
    }
}
