<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ShippingRule;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const CART_SESSION_KEY = 'shopping_cart';

    public function add(Product $product, int $quantity = 1, array $options = [])
    {
        $cart = $this->getCart();
        $cartItemId = $this->generateCartItemId($product, $options);

        if (isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantity'] += $quantity;
        } else {
            $cart[$cartItemId] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'product_price' => $product->getEffectivePrice(),
                'product_image' => $product->getMainImage(),
                'quantity' => $quantity,
                'options' => $options,
                'subtotal' => $product->getEffectivePrice() * $quantity,
            ];
        }

        $this->updateCartTotals($cart);
        $this->saveCart($cart);

        return $cart[$cartItemId];
    }

    public function update(string $cartItemId, int $quantity)
    {
        $cart = $this->getCart();

        if (isset($cart[$cartItemId])) {
            if ($quantity <= 0) {
                unset($cart[$cartItemId]);
            } else {
                $cart[$cartItemId]['quantity'] = $quantity;
                $cart[$cartItemId]['subtotal'] = $cart[$cartItemId]['product_price'] * $quantity;
            }

            $this->updateCartTotals($cart);
            $this->saveCart($cart);
        }

        return $cart;
    }

    public function remove(string $cartItemId)
    {
        $cart = $this->getCart();

        if (isset($cart[$cartItemId])) {
            unset($cart[$cartItemId]);
            $this->updateCartTotals($cart);
            $this->saveCart($cart);
        }

        return $cart;
    }

    public function clear()
    {
        Session::forget(self::CART_SESSION_KEY);
        return [];
    }

    public function getCart()
    {
        return Session::get(self::CART_SESSION_KEY, []);
    }

    public function getCartItems()
    {
        $cart = $this->getCart();
        return array_filter($cart, function($key) {
            return !in_array($key, ['totals']);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getCartTotals()
    {
        $cart = $this->getCart();
        return $cart['totals'] ?? [
            'subtotal' => 0,
            'shipping' => 0,
            'tax' => 0,
            'discount' => 0,
            'total' => 0,
            'items_count' => 0,
            'total_weight' => 0,
        ];
    }

    public function getItemsCount()
    {
        $totals = $this->getCartTotals();
        return $totals['items_count'] ?? 0;
    }

    public function getTotalWeight()
    {
        $cart = $this->getCartItems();
        $totalWeight = 0;

        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->weight) {
                $totalWeight += $product->weight * $item['quantity'];
            }
        }

        return $totalWeight;
    }

    public function calculateShipping(array $shippingAddress = [])
    {
        $totals = $this->getCartTotals();
        $totalWeight = $this->getTotalWeight();
        $subtotal = $totals['subtotal'];

        // Buscar regras de frete aplicáveis
        $shippingRules = ShippingRule::active()
            ->orderBy('sort_order')
            ->get();

        $availableShipping = [];

        foreach ($shippingRules as $rule) {
            $cost = $rule->calculateCost($subtotal, $totalWeight, $shippingAddress);
            
            if ($cost !== null) {
                $delivery = $rule->getEstimatedDelivery();
                
                $availableShipping[] = [
                    'id' => $rule->id,
                    'name' => $rule->name,
                    'description' => $rule->description,
                    'cost' => $cost,
                    'estimated_days_min' => $rule->estimated_days_min,
                    'estimated_days_max' => $rule->estimated_days_max,
                    'estimated_delivery' => $delivery['range'],
                ];
            }
        }

        return $availableShipping;
    }

    public function applyShipping(int $shippingRuleId)
    {
        $cart = $this->getCart();
        $shippingRule = ShippingRule::find($shippingRuleId);

        if (!$shippingRule) {
            throw new \Exception('Regra de frete não encontrada');
        }

        $totals = $this->getCartTotals();
        $totalWeight = $this->getTotalWeight();
        $shippingCost = $shippingRule->calculateCost($totals['subtotal'], $totalWeight);

        if ($shippingCost === null) {
            throw new \Exception('Regra de frete não aplicável');
        }

        $cart['shipping'] = [
            'rule_id' => $shippingRule->id,
            'name' => $shippingRule->name,
            'cost' => $shippingCost,
            'estimated_days_min' => $shippingRule->estimated_days_min,
            'estimated_days_max' => $shippingRule->estimated_days_max,
        ];

        $this->updateCartTotals($cart);
        $this->saveCart($cart);

        return $cart;
    }

    public function applyCoupon(string $couponCode)
    {
        // Implementar sistema de cupons de desconto
        // Por enquanto, retorna erro
        throw new \Exception('Sistema de cupons não implementado ainda');
    }

    public function validateStock()
    {
        $cart = $this->getCartItems();
        $errors = [];

        foreach ($cart as $cartItemId => $item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                $errors[] = "Produto '{$item['product_name']}' não encontrado";
                continue;
            }

            if ($product->status !== 'active') {
                $errors[] = "Produto '{$product->name}' não está mais disponível";
                continue;
            }

            if ($product->manage_stock && !$product->in_stock) {
                $errors[] = "Produto '{$product->name}' está fora de estoque";
                continue;
            }

            if ($product->manage_stock && $product->stock_quantity < $item['quantity']) {
                $errors[] = "Produto '{$product->name}' tem apenas {$product->stock_quantity} unidade(s) disponível(eis)";
                continue;
            }

            // Verificar se o preço mudou
            if ($product->getEffectivePrice() != $item['product_price']) {
                $errors[] = "Preço do produto '{$product->name}' foi atualizado";
            }
        }

        return $errors;
    }

    public function convertToOrder(array $customerData, array $shippingAddress, array $billingAddress)
    {
        $cart = $this->getCart();
        $totals = $this->getCartTotals();

        // Validar estoque antes de criar o pedido
        $stockErrors = $this->validateStock();
        if (!empty($stockErrors)) {
            throw new \Exception('Erro no estoque: ' . implode(', ', $stockErrors));
        }

        // Criar dados do pedido
        $orderData = [
            'order_number' => \App\Models\Order::generateOrderNumber(),
            'client_id' => $customerData['client_id'],
            'status' => 'pending',
            'subtotal' => $totals['subtotal'],
            'shipping_total' => $totals['shipping'],
            'tax_total' => $totals['tax'],
            'discount_total' => $totals['discount'],
            'total' => $totals['total'],
            'currency' => 'BRL',
            'billing_address' => $billingAddress,
            'shipping_address' => $shippingAddress,
            'customer_notes' => $customerData['notes'] ?? null,
        ];

        return $orderData;
    }

    private function generateCartItemId(Product $product, array $options = [])
    {
        return md5($product->id . serialize($options));
    }

    private function updateCartTotals(array &$cart)
    {
        $subtotal = 0;
        $itemsCount = 0;
        $totalWeight = 0;

        foreach ($cart as $key => $item) {
            if ($key === 'totals' || $key === 'shipping') {
                continue;
            }

            $subtotal += $item['subtotal'];
            $itemsCount += $item['quantity'];

            // Calcular peso total
            $product = Product::find($item['product_id']);
            if ($product && $product->weight) {
                $totalWeight += $product->weight * $item['quantity'];
            }
        }

        $shippingCost = isset($cart['shipping']) ? $cart['shipping']['cost'] : 0;
        $tax = 0; // Implementar cálculo de impostos se necessário
        $discount = 0; // Implementar sistema de cupons

        $cart['totals'] = [
            'subtotal' => $subtotal,
            'shipping' => $shippingCost,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $subtotal + $shippingCost + $tax - $discount,
            'items_count' => $itemsCount,
            'total_weight' => $totalWeight,
        ];
    }

    private function saveCart(array $cart)
    {
        Session::put(self::CART_SESSION_KEY, $cart);
    }
}