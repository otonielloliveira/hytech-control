@extends('layouts.blog')

@section('title', 'Loja - Produtos')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Loja</h1>
        <p class="text-gray-600">Confira nossos produtos exclusivos</p>
    </div>

    @if($featuredProducts->count() > 0)
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Produtos em Destaque</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                @include('store.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Todos os Produtos</h2>
        
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    @include('store.partials.product-card', ['product' => $product])
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhum produto encontrado</h3>
                <p class="text-gray-600">Em breve teremos produtos disponíveis para você!</p>
            </div>
        @endif
    </div>
</div>

<!-- Carrinho Sidebar (oculto por padrão) -->
<div id="cart-sidebar" class="fixed right-0 top-0 h-full w-80 bg-white shadow-lg transform translate-x-full transition-transform duration-300 z-50">
    <div class="p-4 border-b">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">Carrinho</h3>
            <button id="close-cart" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div id="cart-content" class="p-4 flex-1 overflow-y-auto">
        <!-- Conteúdo do carrinho será carregado aqui via AJAX -->
    </div>
    <div class="p-4 border-t">
        <a href="{{ route('store.cart') }}" class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition-colors">
            Ver Carrinho Completo
        </a>
    </div>
</div>

<!-- Overlay para o carrinho -->
<div id="cart-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar ao carrinho
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            const quantity = 1;
            
            fetch(`/loja/carrinho/adicionar/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensagem de sucesso
                    showNotification('Produto adicionado ao carrinho!', 'success');
                    
                    // Atualizar contador do carrinho
                    updateCartCounter(data.cart_totals.items_count);
                    
                    // Mostrar carrinho lateral
                    showCartSidebar();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Erro ao adicionar produto ao carrinho', 'error');
            });
        });
    });
    
    // Controles do carrinho lateral
    document.getElementById('close-cart').addEventListener('click', function() {
        hideCartSidebar();
    });
    
    document.getElementById('cart-overlay').addEventListener('click', function() {
        hideCartSidebar();
    });
});

function showCartSidebar() {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');
    
    sidebar.classList.remove('translate-x-full');
    overlay.classList.remove('hidden');
    
    // Carregar conteúdo do carrinho
    loadCartContent();
}

function hideCartSidebar() {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');
    
    sidebar.classList.add('translate-x-full');
    overlay.classList.add('hidden');
}

function loadCartContent() {
    // Implementar carregamento do conteúdo do carrinho via AJAX
    document.getElementById('cart-content').innerHTML = '<p class="text-center text-gray-500">Carregando...</p>';
}

function updateCartCounter(count) {
    const counter = document.querySelector('.cart-counter');
    if (counter) {
        counter.textContent = count;
        counter.classList.toggle('hidden', count === 0);
    }
}

function showNotification(message, type) {
    // Implementar sistema de notificações
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush