@extends('layouts.blog')

@section('title', 'Loja - Produtos')

@section('content')
    <style>
        body {
            background-color: #ededed;
        }
        body {
            background-color: #ededed;
        }

        /* Container principal */
        .store-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem 1rem;
        }

        /* Header da loja */
        .store-header {
            background: linear-gradient(135deg, #fff159 0%, #ffe600 100%);
            border-radius: 6px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 2px rgba(0,0,0,.1);
        }

        .store-header h1 {
            font-size: 28px;
            font-weight: 400;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .store-header p {
            font-size: 16px;
            color: #666;
            margin: 0;
        }

        /* Cards de produto estilo ML */
        .product-card {
            background: #fff;
            border-radius: 6px;
            transition: all 0.2s ease;
            border: none;
            box-shadow: 0 1px 2px rgba(0,0,0,.1);
            height: 100%;
        }

        .product-card:hover {
            box-shadow: 0 7px 16px rgba(0,0,0,.15);
            transform: translateY(-2px);
        }

        /* Imagem do produto */
        .product-image {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 6px 6px 0 0;
        }

        .product-image-container {
            background: #fff;
            position: relative;
            overflow: hidden;
            border-radius: 6px 6px 0 0;
        }

        /* Badge de desconto */
        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #00a650;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Preços */
        .price-current {
            font-size: 24px;
            font-weight: 300;
            color: #333;
            margin: 0;
        }

        .price-original {
            font-size: 14px;
            color: #999;
            text-decoration: line-through;
            margin: 0;
        }

        .installments {
            font-size: 13px;
            color: #00a650;
            margin: 0;
        }

        /* Section título */
        .section-title {
            font-size: 22px;
            font-weight: 400;
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e6e6e6;
        }

        /* Grid de produtos */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 576px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }
        }

        @media (min-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (min-width: 992px) {
            .products-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        /* Botão adicionar ao carrinho */
        .btn-add-cart {
            background: #3483fa;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            width: 100%;
        }

        .btn-add-cart:hover {
            background: #2968c8;
            color: white;
        }

        .btn-add-cart:disabled {
            background: #e6e6e6;
            color: #999;
        }

        /* Frete grátis badge */
        .free-shipping {
            color: #00a650;
            font-size: 13px;
            font-weight: 500;
        }

        /* Estado vazio */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,.1);
        }

        .empty-state i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 20px;
            font-weight: 400;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            font-size: 16px;
            color: #999;
            margin: 0;
        }
    </style>

    <div class="store-container">
        <!-- Header da Loja -->
        <div class="store-header">
            <h1>🛍️ Nossa Loja</h1>
            <p>Confira produtos exclusivos relacionados ao nosso conteúdo — livros, camisetas e muito mais!</p>
        </div>

        <!-- Produtos em Destaque -->
        @if ($featuredProducts->count() > 0)
            <section class="mb-4">
                <h2 class="section-title">✨ Produtos em Destaque</h2>
                <div class="products-grid">
                    @foreach ($featuredProducts as $product)
                        @include('store.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Todos os Produtos -->
        <section class="mb-4">
            <h2 class="section-title">📦 Todos os Produtos</h2>
            
            @if ($products->count() > 0)
                <div class="products-grid">
                    @foreach ($products as $product)
                        @include('store.partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>Nenhum produto encontrado</h3>
                    <p>Em breve teremos produtos disponíveis para você!</p>
                </div>
            @endif
        </section>
    </div>

    {{-- Carrinho lateral --}}
    <div id="cart-sidebar" class="position-fixed top-0 end-0 h-100 bg-white shadow-lg"
        style="width: 380px; z-index: 1050; transform: translateX(100%); transition: transform 0.3s ease;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: #fff;">
            <h3 class="h5 fw-semibold text-dark mb-0">
                <i class="fas fa-shopping-cart me-2 text-primary"></i>Meu Carrinho
            </h3>
            <button id="close-cart" class="btn btn-sm btn-light">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="cart-content" class="p-3 overflow-auto" style="height: calc(100% - 140px);">
            <p class="text-center text-muted">Seu carrinho está vazio</p>
        </div>
        <div class="p-3 border-top bg-light">
            <a href="{{ route('store.cart') }}" class="btn btn-primary w-100 py-2">
                <i class="fas fa-shopping-bag me-2"></i>Ver Carrinho Completo
            </a>
        </div>
    </div>

    <div id="cart-overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none"
        style="z-index: 1040;"></div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Código do carrinho
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.dataset.productId;
                    fetch(`/loja/carrinho/adicionar/${productId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
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
                                localStorage.setItem('cart_count', data.cart_totals.items_count);
                                showCartSidebar();
                            } else {
                                showNotification(data.message, 'error');
                            }
                        })
                        .catch(() => showNotification('Erro ao adicionar produto ao carrinho',
                            'error'));
                });
            });

            document.getElementById('close-cart').addEventListener('click', hideCartSidebar);
            document.getElementById('cart-overlay').addEventListener('click', hideCartSidebar);
        });

        function showCartSidebar() {
            const sidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('cart-overlay');

            sidebar.style.transform = 'translateX(0)';
            overlay.classList.remove('d-none');
            loadCartContent();
        }

        function hideCartSidebar() {
            const sidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('cart-overlay');

            sidebar.style.transform = 'translateX(100%)';
            overlay.classList.add('d-none');
        }

        function loadCartContent() {
            const cartContent = document.getElementById('cart-content');
            cartContent.innerHTML = '<p class="text-center text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Carregando...</p>';
            
            fetch('{{ route("store.cart") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.items && data.items.length > 0) {
                    let html = '';
                    data.items.forEach(item => {
                        html += `
                            <div class="cart-item mb-3 pb-3 border-bottom">
                                <div class="d-flex gap-2">
                                    <img src="${item.product_image}" alt="${item.product_name}" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1" style="font-size: 13px;">${item.product_name}</h6>
                                        <p class="mb-1 text-muted" style="font-size: 12px;">Qtd: ${item.quantity}</p>
                                        <p class="mb-0 fw-bold text-primary" style="font-size: 14px;">
                                            R$ ${parseFloat(item.subtotal).toFixed(2).replace('.', ',')}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += `
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <strong>R$ ${parseFloat(data.totals.subtotal).toFixed(2).replace('.', ',')}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Frete:</span>
                                <strong>R$ ${parseFloat(data.totals.shipping).toFixed(2).replace('.', ',')}</strong>
                            </div>
                        </div>
                    `;
                    
                    cartContent.innerHTML = html;
                } else {
                    cartContent.innerHTML = '<p class="text-center text-muted">Seu carrinho está vazio</p>';
                }
            })
            .catch(error => {
                console.error('Erro ao carregar carrinho:', error);
                cartContent.innerHTML = '<p class="text-center text-muted">Seu carrinho está vazio</p>';
            });
        }

        function updateCartCounter(count) {
            const counter = document.querySelector('.cart-counter');
            if (counter) {
                counter.textContent = count;
                if (count > 0) {
                    counter.style.display = 'flex';
                } else {
                    counter.style.display = 'none';
                }
            }
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `position-fixed top-0 end-0 m-3 p-3 rounded shadow text-white fw-medium ${
        type === 'success' ? 'bg-success' : 'bg-danger'
    }`;
            notification.style.zIndex = '9999';
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }
    </script>
@endsection
