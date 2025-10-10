<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PaymentMethod;
use App\Services\CartService;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    protected $cartService;
    protected $paymentGatewayService;

    public function __construct(CartService $cartService, PaymentGatewayService $paymentGatewayService)
    {
        $this->cartService = $cartService;
        $this->paymentGatewayService = $paymentGatewayService;
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
        // Dados de exemplo para testar a interface
        $cartItems = [
            'item_1' => [
                'product_id' => 1,
                'product_name' => 'Livro: História do Brasil',
                'product_sku' => 'LIVRO-HB-001',
                'product_price' => 89.90,
                'product_image' => asset('images/livro-historia-brasil.jpg'),
                'quantity' => 3,
                'subtotal' => 269.70,
            ],
            'item_2' => [
                'product_id' => 2,
                'product_name' => 'Livro - Fé e Política de mãos dadass',
                'product_sku' => 'LIVRO-FP-002',
                'product_price' => 86.90,
                'product_image' => asset('images/livro-fe-politica.jpg'),
                'quantity' => 1,
                'subtotal' => 86.90,
            ]
        ];
        
        $cartTotals = [
            'subtotal' => 356.60,
            'shipping' => 16.90,
            'total' => 373.50,
            'item_count' => 4
        ];

        // Tentar usar o serviço se existir, senão usar dados mock
        try {
            $serviceCartItems = $this->cartService->getCartItems();
            $serviceCartTotals = $this->cartService->getCartTotals();
            
            if (!empty($serviceCartItems)) {
                $cartItems = $serviceCartItems;
                $cartTotals = $serviceCartTotals;
            }
        } catch (\Exception $e) {
            // Use mock data if service fails
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
        $paymentMethods = PaymentMethod::active()->orderBy('sort_order')->get();

        return view('store.checkout', compact('cartItems', 'cartTotals', 'paymentMethods'));
    }

    public function processCheckout(Request $request)
    {
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

            // Processar pagamento
            $paymentMethod = PaymentMethod::find($request->payment_method_id);
            $paymentResult = $this->paymentGatewayService->processPayment($order, $paymentMethod, $request->payment_data ?? []);

            if ($paymentResult['success']) {
                $order->update([
                    'payment_method_id' => $paymentMethod->id,
                    'payment_transaction_id' => $paymentResult['transaction_id'],
                    'payment_status' => 'processing',
                ]);

                // Limpar carrinho
                $this->cartService->clear();

                return redirect()->route('store.order.success', $order->id)
                    ->with('payment_result', $paymentResult);
            } else {
                // Reverter estoque em caso de erro no pagamento
                foreach ($cartItems as $item) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->increaseStock($item['quantity']);
                    }
                }

                $order->update(['payment_status' => 'failed']);

                return redirect()->back()
                    ->with('error', 'Erro no pagamento: ' . $paymentResult['message'])
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

        $address = $client->addresses()->where('type', 'billing')->first();
        if ($address) {
            $address->update($addressData);
        } else {
            $client->addresses()->create($addressData);
        }

        return $client;
    }
}
