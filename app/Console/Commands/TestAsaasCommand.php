<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentMethod;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;

use Exception;
use App\Services\Payment\Gateways\AsaasService;

class TestAsaasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:asaas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting ASAAS test...');

        $pm = PaymentMethod::where('gateway', 'asaas')->first();
        if (!$pm) {
            $this->info('ASAAS payment method not found, creating a sandbox entry using ASAAS_API_KEY from env (if available)');
            $config = [
                'api_key' => env('ASAAS_API_KEY') ?: null,
                'environment' => env('ASAAS_ENVIRONMENT', 'sandbox'),
            ];
            $pm = PaymentMethod::create([
                'name' => 'ASAAS (Sandbox)',
                'slug' => 'asaas-sandbox',
                'description' => 'ASAAS Sandbox gateway auto-created by test command',
                'gateway' => 'asaas',
                'config' => $config,
                'is_active' => true,
                'sort_order' => 100,
            ]);
            $this->line('Created PaymentMethod ASAAS id: ' . $pm->id);
        }

        $client = Client::first() ?? Client::create(['name' => 'Sandbox Test', 'email' => 'sandbox@test.local', 'password' => bcrypt('secret')]);

        $order = Order::create([
            'order_number' => 'TEST' . time(),
            'client_id' => $client->id,
            // required numeric totals (subtotal is NOT NULL in migration)
            'subtotal' => 10.00,
            'total' => 10.00,
            // optional but explicit
            'shipping_total' => 0.00,
            'tax_total' => 0.00,
            'discount_total' => 0.00,
            'billing_address' => ['name' => $client->name, 'email' => $client->email, 'address' => 'Rua Teste, 1', 'city' => 'SP', 'state' => 'SP', 'zip' => '38406-289'],
            'shipping_address' => ['name' => $client->name, 'address' => 'Rua Teste, 1', 'city' => 'SP', 'state' => 'SP', 'zip' => '38406-289'],
            'status' => 'pending'
        ]);

        // Ensure there's a product to reference (order_items.product_id FK)
        $product = Product::first();
        if (!$product) {
            $product = Product::create([
                'name' => 'Sandbox Product',
                'sku' => 'SANDBOX',
                'price' => 10.00,
                'description' => 'Temporary product for ASAAS test',
                'is_active' => false,
            ]);
            $this->line('Created temporary Product id: ' . $product->id);
        }

        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'product_sku' => $product->sku ?? 'SANDBOX', 'product_price' => 10.00, 'quantity' => 1, 'total' => 10.00]);

        // Call AsaasService directly so we can provide a detailed payer payload
        $config = $pm->getGatewayConfig();
        $asaas = new AsaasService($config);

        // Quick connection test
        $this->info('Testing ASAAS connection...');
        $connected = $asaas->testConnection();
        if (!$connected) {
            $this->error('ASAAS testConnection failed. Please verify ASAAS_API_KEY and network access to the sandbox API.');
            $this->line('PaymentMethod config preview: ' . json_encode($config));
            return 1;
        }

        // Prepare customer data and items for checkout (avoid creating customer id directly)
        $customerData = [
            'name' => $client->name,
            'email' => $client->email,
            // ASAAS expects camelCase keys and a valid-looking CPF/CNPJ (digits only)
            'cpfCnpj' => $this->generateCpf(),
            'phone' => '34998175740',
            'address' => 'Rua Teste',
            'addressNumber' => '1',
            'postalCode' => '38406-289',
            'city' => 'São Paulo',
            'state' => 'SP',
            'province' => 'SP',
        ];

        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'name' => $item->product_name,
                'value' => (float) $item->product_price,
                'quantity' => (int) $item->quantity,
            ];
        }

    // Provide callback and redirect URLs required by ASAAS sandbox
    $callbackUrl = env('ASAAS_WEBHOOK_URL', 'https://hytech-control.test/webhook/asaas');
    $successUrl = env('APP_URL', 'https://hytech-control.test') . '/payment/success';
    $cancelUrl = env('APP_URL', 'https://hytech-control.test') . '/payment/cancel';

        try {
            $result = $asaas->createPixCheckout([
                'items' => $items,
                'customer_data' => $customerData,
                'external_reference' => $order->id,
                // send callback as an object (url + method) to match API expectations
                'callback' => [
                    'expiredUrl' => $expiredUrl ?? $callbackUrl,
                    'successUrl' => $successUrl,
                    'cancelUrl' => $cancelUrl,
                ],
                'minutes_to_expire' => 60,
            ]);

            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return $result['success'] ? 0 : 1;
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Generate a syntactically valid CPF (returns digits only)
     *
     * @return string
     */
    private function generateCpf()
    {
        // generate 9 random digits
        $n = [];
        for ($i = 0; $i < 9; $i++) {
            $n[$i] = mt_rand(0, 9);
        }

        // first check digit
        $sum = 0;
        for ($i = 0, $j = 10; $i < 9; $i++, $j--) {
            $sum += $n[$i] * $j;
        }
        $r = $sum % 11;
        $d1 = ($r < 2) ? 0 : 11 - $r;

        // second check digit
        $sum = 0;
        for ($i = 0, $j = 11; $i < 9; $i++, $j--) {
            $sum += $n[$i] * $j;
        }
        $sum += $d1 * 2;
        $r = $sum % 11;
        $d2 = ($r < 2) ? 0 : 11 - $r;

        return implode('', $n) . $d1 . $d2;
    }
}
