@extends('layouts.blog')

@section('title', 'Carrinho - Loja')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Carrinho de Compras</h1>
    
    @if(count($cartItems) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $itemId => $item)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 cart-item" data-item-id="{{ $itemId }}">
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                <img src="{{ $item['product_image'] }}" alt="{{ $item['product_name'] }}" 
                                     class="w-full h-full object-cover">
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $item['product_name'] }}</h3>
                                <p class="text-sm text-gray-600">SKU: {{ $item['product_sku'] }}</p>
                                <p class="text-lg font-bold text-blue-600">R$ {{ number_format($item['product_price'], 2, ',', '.') }}</p>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <button onclick="updateQuantity('{{ $itemId }}', -1)" class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="w-12 text-center font-semibold quantity-display">{{ $item['quantity'] }}</span>
                                <button onclick="updateQuantity('{{ $itemId }}', 1)" class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            
                            <div class="text-right">
                                <p class="font-bold text-lg">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</p>
                                <button onclick="removeItem('{{ $itemId }}')" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash mr-1"></i>Remover
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Cart Summary -->
            <div class="lg:col-span-1">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 sticky top-4">
                    <h3 class="text-lg font-semibold mb-4">Resumo do Pedido</h3>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span id="cart-subtotal">R$ {{ number_format($cartTotals['subtotal'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Frete:</span>
                            <span id="cart-shipping">R$ {{ number_format($cartTotals['shipping'], 2, ',', '.') }}</span>
                        </div>
                        @if($cartTotals['discount'] > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Desconto:</span>
                                <span>-R$ {{ number_format($cartTotals['discount'], 2, ',', '.') }}</span>
                            </div>
                        @endif
                        <hr>
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total:</span>
                            <span id="cart-total">R$ {{ number_format($cartTotals['total'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="{{ route('store.checkout') }}" class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            <i class="fas fa-credit-card mr-2"></i>
                            Finalizar Compra
                        </a>
                        <a href="{{ route('store.index') }}" class="block w-full bg-gray-200 text-gray-800 text-center py-3 rounded-lg hover:bg-gray-300 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Continuar Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16">
            <div class="text-gray-400 text-6xl mb-4">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Seu carrinho está vazio</h2>
            <p class="text-gray-600 mb-8">Adicione alguns produtos ao seu carrinho para continuar</p>
            <a href="{{ route('store.index') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                <i class="fas fa-shopping-bag mr-2"></i>
                Ir às Compras
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function updateQuantity(itemId, change) {
    const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
    const quantityDisplay = cartItem.querySelector('.quantity-display');
    const currentQuantity = parseInt(quantityDisplay.textContent);
    const newQuantity = Math.max(0, currentQuantity + change);
    
    if (newQuantity === 0) {
        removeItem(itemId);
        return;
    }
    
    updateCart([{id: itemId, quantity: newQuantity}]);
}

function removeItem(itemId) {
    if (confirm('Tem certeza que deseja remover este item?')) {
        fetch('/loja/carrinho/remover', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ cart_item_id: itemId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function updateCart(items) {
    fetch('/loja/carrinho/atualizar', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ items: items })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endpush
@endsection