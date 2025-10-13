@extends('layouts.blog')

@section('title', 'Loja - Produtos')

@section('content')
<style>
    /* Animação suave */
    .product-card {
        transition: all 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
    }

    /* Linha de corte */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Ajuste de colunas no mobile */
    @media (max-width: 640px) {
        .grid {
            gap: 1rem;
        }
    }
</style>

<div class="container mx-auto px-4 py-12">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-3">🛍️ Nossa Loja</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">Confira produtos exclusivos relacionados ao nosso conteúdo — livros, camisetas, e muito mais!</p>
    </div>

    @if($featuredProducts->count() > 0)
    <section class="mb-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">✨ Produtos em Destaque</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($featuredProducts as $product)
            @include('store.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </section>
    @endif

    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">📦 Todos os Produtos</h2>

        @if($products->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($products as $product)
            @include('store.partials.product-card', ['product' => $product])
            @endforeach
        </div>

        <div class="mt-10 flex justify-center">
            {{ $products->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">
                <i class="fas fa-box-open"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhum produto encontrado</h3>
            <p class="text-gray-600">Em breve teremos produtos disponíveis para você!</p>
        </div>
        @endif
    </section>
</div>

{{-- Carrinho lateral --}}
<div id="cart-sidebar" class="fixed right-0 top-0 h-full w-80 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-50 rounded-l-lg">
    <div class="p-4 border-b flex justify-between items-center bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-700">Carrinho</h3>
        <button id="close-cart" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div id="cart-content" class="p-4 flex-1 overflow-y-auto text-gray-700">
        <p class="text-center text-gray-500">Seu carrinho está vazio</p>
    </div>
    <div class="p-4 border-t bg-gray-50">
        <a href="{{ route('store.cart') }}" class="block w-full bg-indigo-600 text-white text-center py-3 rounded-lg hover:bg-indigo-700 transition-colors">
            Ver Carrinho Completo
        </a>
    </div>
</div>

<div id="cart-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40"></div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.productId;
                fetch(`/loja/carrinho/adicionar/${productId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            quantity: 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Produto adicionado ao carrinho!', 'success');
                            updateCartCounter(data.cart_totals.items_count);
                            showCartSidebar();
                        } else {
                            showNotification(data.message, 'error');
                        }
                    })
                    .catch(() => showNotification('Erro ao adicionar produto ao carrinho', 'error'));
            });
        });

        document.getElementById('close-cart').addEventListener('click', hideCartSidebar);
        document.getElementById('cart-overlay').addEventListener('click', hideCartSidebar);
    });

    function showCartSidebar() {
        document.getElementById('cart-sidebar').classList.remove('translate-x-full');
        document.getElementById('cart-overlay').classList.remove('hidden');
        loadCartContent();
    }

    function hideCartSidebar() {
        document.getElementById('cart-sidebar').classList.add('translate-x-full');
        document.getElementById('cart-overlay').classList.add('hidden');
    }

    function loadCartContent() {
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
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 text-white font-medium ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
</script>
@endpush