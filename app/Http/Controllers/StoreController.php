<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentGatewayConfig;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Services\CartService;
use App\Services\PaymentGatewayService;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    protected $cartService;
    protected $paymentGatewayService;
    protected $paymentManager;

    public function __construct(CartService $cartService, PaymentGatewayService $paymentGatewayService, PaymentManager $paymentManager)
    {
        $this->cartService = $cartService;
        $this->paymentGatewayService = $paymentGatewayService;
        $this->paymentManager = $paymentManager;
    }

    public function index()
    {
        $products = Product::active()
            ->inStock()
            ->orderBy('featured', 'desc')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $featuredProducts = Product::active()
            ->featured()
            ->inStock()
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        return view('store.index', compact('products', 'featuredProducts'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->where('status', 'active')->firstOrFail();

        $relatedProducts = Product::active()
            ->inStock()
            ->where('id', '!=', $product->id)
            ->orderBy('featured', 'desc')
            ->take(4)
            ->get();

        return view('store.product', compact('product', 'relatedProducts'));
    }

    public function addToCart(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        if ($product->status !== 'active' || !$product->in_stock) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não disponível'
            ], 400);
        }

        if ($product->manage_stock && $product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Quantidade não disponível em estoque'
            ], 400);
        }

        $cartItem = $this->cartService->add($product, $request->quantity);
        $cartTotals = $this->cartService->getCartTotals();

        return response()->json([
            'success' => true,
            'message' => 'Produto adicionado ao carrinho',
            'cart_item' => $cartItem,
            'cart_totals' => $cartTotals
        ]);
    }

    public function cart()
    {
        $cartItems = $this->cartService->getCartItems();
        $cartTotals = $this->cartService->getCartTotals();

        // Se for requisição AJAX, retorna JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'items' => array_values($cartItems), // Converte para array indexado
                'totals' => $cartTotals
            ]);
        }

        return view('store.cart', compact('cartItems', 'cartTotals'));
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:0|max:100',
        ]);

        foreach ($request->items as $item) {
            $this->cartService->update($item['id'], $item['quantity']);
        }

        $cartTotals = $this->cartService->getCartTotals();

        return response()->json([
            'success' => true,
            'message' => 'Carrinho atualizado',
            'cart_totals' => $cartTotals
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|string',
        ]);

        $this->cartService->remove($request->cart_item_id);
        $cartTotals = $this->cartService->getCartTotals();

        return response()->json([
            'success' => true,
            'message' => 'Item removido do carrinho',
            'cart_totals' => $cartTotals
        ]);
    }

    public function clearCart()
    {
        $this->cartService->clear();

        return response()->json([
            'success' => true,
            'message' => 'Carrinho limpo'
        ]);
    }

    public function checkout()
    {
        $cartItems = $this->cartService->getCartItems();
        if (empty($cartItems)) {
            return redirect()->route('store.cart')->with('error', 'Seu carrinho está vazio');
        }

        $stockErrors = $this->cartService->validateStock();
        if (!empty($stockErrors)) {
            return redirect()->route('store.cart')->with('error', 'Alguns itens não estão disponíveis: ' . implode(', ', $stockErrors));
        }

        $cartTotals = $this->cartService->getCartTotals();
        
        // Verificar se PIX Manual está ativo
        $activeGateway = $this->paymentManager->getActiveGateway();
        $isPixManual = $activeGateway && $activeGateway->gateway_type === 'pix_manual';
        
        // Se PIX Manual estiver ativo, mostrar apenas opção PIX
        if ($isPixManual) {
            $paymentMethods = PaymentMethod::active()
                ->where('gateway', 'pix')
                ->orderBy('sort_order')
                ->get();
        } else {
            $paymentMethods = PaymentMethod::active()->orderBy('sort_order')->get();
        }

        $client = null;
        $addresses = [];
        if (Auth::guard('client')->check()) {
            $client = Auth::guard('client')->user();
            $addresses = $client->addresses()->orderBy('is_default', 'desc')->get();
        }

        return view('store.checkout', compact('cartItems', 'cartTotals', 'paymentMethods', 'client', 'addresses', 'isPixManual'));
    }

    public function processCheckout(Request $request)
    {
        // If the client selected an existing address (use_address), and is authenticated,
        // load that address and merge its data into the request so validation passes.
        if ($request->has('use_address') && 
            Auth::guard('client')->check() && 
            $request->input('use_address')) {
            $client = Auth::guard('client')->user();
            $addr = $client->addresses()->where('id', $request->input('use_address'))->first();
            if ($addr) {
                $request->merge([
                    'billing_address' => $addr->street . ($addr->number ? ', ' . $addr->number : '' ) . ($addr->complement ? ' - ' . $addr->complement : ''),
                    'billing_city' => $addr->city,
                    'billing_state' => $addr->state,
                    'billing_zip' => $addr->postal_code,
                ]);
            }
        }

        $request->validate([
            'billing_name' => 'required|string|max:255',
            'billing_email' => 'required|email',
            'billing_phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:2',
            'billing_zip' => 'required|string|max:10',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'terms_accepted' => 'required|accepted',
        ]);

        $cartItems = $this->cartService->getCartItems();
        
        if (empty($cartItems)) {
            return redirect()->route('store.cart')->with('error', 'Seu carrinho está vazio');
        }

        $stockErrors = $this->cartService->validateStock();
        if (!empty($stockErrors)) {
            return redirect()->route('store.cart')->with('error', 'Alguns itens não estão disponíveis');
        }

        // Criar ou atualizar cliente
        $client = $this->createOrUpdateClient($request);

        // Preparar dados do pedido
        $billingAddress = [
            'name' => $request->billing_name,
            'email' => $request->billing_email,
            'phone' => $request->billing_phone,
            'address' => $request->billing_address,
            'city' => $request->billing_city,
            'state' => $request->billing_state,
            'zip' => $request->billing_zip,
        ];

        $shippingAddress = $request->same_address ? $billingAddress : [
            'name' => $request->shipping_name,
            'address' => $request->shipping_address,
            'city' => $request->shipping_city,
            'state' => $request->shipping_state,
            'zip' => $request->shipping_zip,
        ];

        $customerData = [
            'client_id' => $client->id,
            'notes' => $request->notes,
        ];

        try {
            $orderData = $this->cartService->convertToOrder($customerData, $shippingAddress, $billingAddress);
            
            // Criar pedido
            $order = \App\Models\Order::create($orderData);

            // Criar itens do pedido
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'],
                    'product_price' => $item['product_price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['subtotal'],
                    'product_meta' => $item['options'] ?? [],
                ]);

                // Decrementar estoque
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->decreaseStock($item['quantity']);
                }
            }

            // Processar pagamento usando PaymentManager
            $selectedPaymentMethod = PaymentMethod::find($request->payment_method_id);
            
            if (!$selectedPaymentMethod) {
                throw new \Exception('Método de pagamento não encontrado');
            }

            // Verificar se há um gateway ativo e configurado
            $activeGateway = $this->paymentManager->getActiveGateway();
            if (!$activeGateway) {
                throw new \Exception('Nenhum gateway de pagamento está ativo. Por favor, configure um gateway de pagamento no painel administrativo.');
            }
            
            if (!$activeGateway->isConfigured()) {
                throw new \Exception('O gateway de pagamento (' . $activeGateway->name . ') está ativo mas não está configurado corretamente. Por favor, configure as credenciais no painel administrativo.');
            }

            // Preparar dados do pagador
            $payer = [
                'name' => $billingAddress['name'],
                'email' => $billingAddress['email'],
                'phone' => $billingAddress['phone'],
                'document' => $request->document ?? null,
            ];

            // Preparar opções do pagamento
            $paymentOptions = [
                'description' => "Pedido #{$order->id} - Loja Online",
                'external_reference' => $order->id,
                'metadata' => [
                    'order_id' => $order->id,
                    'customer_name' => $billingAddress['name'],
                    'customer_email' => $billingAddress['email'],
                ]
            ];

            // Processar pagamento baseado no gateway
            $paymentResult = null;
            $gateway = $selectedPaymentMethod->gateway;
            
            // Verificar se é PIX Manual
            $isPixManual = $activeGateway->gateway_type === 'pix_manual';

            switch ($gateway) {
                case 'pix':
                    $paymentResult = $this->paymentManager->createPixPayment(
                        $order, 
                        $payer, 
                        $order->total, 
                        $paymentOptions
                    );
                    
                    // Se for PIX Manual, adicionar dados da chave ao resultado
                    if ($isPixManual && $paymentResult['success']) {
                        $payment = $paymentResult['payment'];
                        $gatewayConfig = $activeGateway->config;
                        
                        // Detectar tipo de chave PIX
                        $pixKey = $gatewayConfig['pix_key'] ?? '';
                        $pixKeyType = $this->detectPixKeyType($pixKey);
                        
                        // Adicionar ao resultado do pagamento
                        $paymentResult['pix_manual_data'] = [
                            'pix_key' => $pixKey,
                            'pix_key_type' => $pixKeyType,
                            'beneficiary_name' => $gatewayConfig['beneficiary_name'] ?? 'Não informado',
                        ];
                    }
                    break;

                case 'card':
                case 'credit_card':
                case 'card_gateway':
                    // Decodificar dados do cartão enviados pelo frontend
                    $paymentData = $request->payment_data ? json_decode($request->payment_data, true) : [];
                    $cardData = $paymentData['card'] ?? [];
                    
                    if (empty($cardData)) {
                        throw new \Exception('Dados do cartão não fornecidos');
                    }

                    $paymentResult = $this->paymentManager->createCreditCardPayment(
                        $order, 
                        $payer, 
                        $order->total, 
                        $cardData,
                        $paymentOptions
                    );
                    break;

                case 'boleto':
                case 'bank_slip':
                    $paymentResult = $this->paymentManager->createBankSlipPayment(
                        $order, 
                        $payer, 
                        $order->total, 
                        $paymentOptions
                    );
                    break;

                default:
                    throw new \Exception("Gateway '{$gateway}' não suportado");
            }

            // Processar resultado do pagamento
            if ($paymentResult['success']) {
                $payment = $paymentResult['payment'];
                $gatewayResponse = $paymentResult['gateway_response'] ?? [];

                // Associar payment ao order (já foi criado pelo PaymentManager)
                // O payment já tem payable_type e payable_id configurados
                
                // Atualizar pedido com informações do pagamento
                $order->update([
                    'payment_method_id' => $selectedPaymentMethod->id,
                    'payment_transaction_id' => $payment->transaction_id,
                    'payment_status' => $payment->status === 'approved' ? 'paid' : 'pending',
                ]);

                // Preparar dados para a view de sucesso
                $normalized = [
                    'success' => true,
                    'transaction_id' => $payment->transaction_id,
                    'gateway_transaction_id' => $payment->gateway_transaction_id,
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                    'amount' => $payment->amount,
                    'is_pix_manual' => $isPixManual,
                    'payment' => [
                        'qr_code_base64' => $payment->qr_code_base64,
                        'qr_code_url' => $payment->qr_code_url,
                        'pix_code' => $payment->pix_code,
                        'checkout_url' => $payment->checkout_url,
                    ],
                ];
                
                // Se for PIX Manual, adicionar dados da chave
                if ($isPixManual && isset($paymentResult['pix_manual_data'])) {
                    $normalized['pix_manual_data'] = $paymentResult['pix_manual_data'];
                }

                // Para PIX, adicionar QR Code no formato legado se necessário
                if ($gateway === 'pix' && $payment->qr_code_base64) {
                    $normalized['qr_code'] = [
                        'qr_code_image' => 'data:image/png;base64,' . $payment->qr_code_base64,
                        'qr_code_text' => $payment->pix_code,
                    ];
                }

                // Para Boleto, adicionar URL
                if (in_array($gateway, ['boleto', 'bank_slip']) && $payment->checkout_url) {
                    $normalized['bank_slip_url'] = $payment->checkout_url;
                }

                // Limpar carrinho
                $this->cartService->clear();

                return redirect()->route('store.order.success', $order->id)
                    ->with('payment_result', $normalized)
                    ->with('success', 'Pedido realizado com sucesso!');

            } else {
                // Erro no pagamento - reverter estoque
                foreach ($cartItems as $item) {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->manage_stock) {
                        $product->increaseStock($item['quantity']);
                    }
                }

                $order->update(['payment_status' => 'failed']);

                $errorMessage = $paymentResult['error'] ?? 'Erro ao processar pagamento';

                return redirect()->back()
                    ->with('error', 'Erro no pagamento: ' . $errorMessage)
                    ->withInput();
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao processar pedido: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function orderSuccess($orderId)
    {
        $order = \App\Models\Order::with(['items.product', 'paymentMethod'])->findOrFail($orderId);
        
        return view('store.order-success', compact('order'));
    }

    private function createOrUpdateClient(Request $request)
    {
        $clientData = [
            'name' => $request->billing_name,
            'email' => $request->billing_email,
            'phone' => $request->billing_phone,
        ];

        // Verificar se já existe um cliente com este email
        $client = \App\Models\Client::where('email', $request->billing_email)->first();

        if ($client) {
            $client->update($clientData);
        } else {
            $client = \App\Models\Client::create($clientData);
        }

        // Criar ou atualizar endereço
        $addressData = [
            'client_id' => $client->id,
            'type' => 'billing',
            'address' => $request->billing_address,
            'city' => $request->billing_city,
            'state' => $request->billing_state,
            'zip_code' => $request->billing_zip,
            'is_default' => true,
        ];

        $address = $client->addresses()->where('is_default', true)->first();
        if ($address) {
            $address->update($addressData);
        } else {
            $client->addresses()->create($addressData);
        }

        return $client;
    }
    
    /**
     * Detecta o tipo de chave PIX baseado no formato
     */
    private function detectPixKeyType($key)
    {
        // Remove espaços e caracteres especiais para análise
        $cleanKey = preg_replace('/[^a-zA-Z0-9@.\-]/', '', $key);
        
        // CPF (11 dígitos)
        if (preg_match('/^\d{11}$/', $cleanKey)) {
            return 'CPF';
        }
        
        // CNPJ (14 dígitos)
        if (preg_match('/^\d{14}$/', $cleanKey)) {
            return 'CNPJ';
        }
        
        // Email
        if (filter_var($key, FILTER_VALIDATE_EMAIL)) {
            return 'Email';
        }
        
        // Telefone (10 ou 11 dígitos, com ou sem código do país)
        if (preg_match('/^\d{10,13}$/', $cleanKey)) {
            return 'Telefone';
        }
        
        // Chave aleatória (UUID format)
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $cleanKey)) {
            return 'Chave Aleatória';
        }
        
        return 'Outro';
    }
}
