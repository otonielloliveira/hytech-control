@extends('layouts.blog')

@section('title', 'Loja - Produtos')

@push('styles')
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endpush

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

    /* Linha de corte aprimorada */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.4rem;
        line-height: 1.2;
    }

    /* Object fit para imagens */
    .object-fit-cover {
        object-fit: cover;
    }

    /* Layout com largura fixa */
    .product-grid-fixed {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    .product-card-wrapper {
        width: 280px;
        flex: 0 0 280px;
    }
    
    .product-card-wrapper .card {
        height: 100%;
        width: 100%;
    }

    /* Container do carrossel */
    .carousel-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 100px; /* Ainda mais espaço para os botões ficarem fora */
        position: relative;
        margin-left: 160px;
    }
    
    /* Botões de navegação externos */
    .carousel-nav-next,
    .carousel-nav-prev {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 50%;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        color: #007bff;
        z-index: 20;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        outline: none;
    }
    
    .carousel-nav-next {
        float: right;
        right: -140px; /* Posicionado completamente fora da área dos cards */
    }
    
    .carousel-nav-prev {
        float: left;
        left: 100px; /* Posicionado completamente fora da área dos cards */
    }
    
    .carousel-nav-next i,
    .carousel-nav-prev i {
        font-size: 20px;
        color: #007bff;
        line-height: 1;
    }
    
    .carousel-nav-next:hover,
    .carousel-nav-prev:hover {
        background: #007bff;
        color: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }
    
    .carousel-nav-next:hover i,
    .carousel-nav-prev:hover i {
        color: white;
    }

    /* Swiper Carousel Styles */
    .products-swiper {
        width: 100%;
        padding: 20px 5px 50px 5px; /* Padding menor já que o container tem mais espaço */
        position: relative;
        overflow: visible; /* Permitir que os cards sejam visíveis fora da área */
    }
    
    .products-swiper .swiper-wrapper {
        display: flex;
        align-items: stretch;
        transition-timing-function: linear;
    }
    
    .products-swiper .swiper-slide {
        height: auto;
        display: flex !important;
        flex-direction: column;
        align-items: stretch;
        flex-shrink: 0;
        width: 280px !important; /* Largura fixa */
        max-width: 280px;
        padding: 0 5px; /* Padding extra para espaçamento visual */
    }
    
    .products-swiper .swiper-slide .card {
        height: 100%;
        flex: 1;
        margin: 0;
        width: 100%;
        max-width: 280px;
    }

    /* Customização da paginação */
    .products-swiper .swiper-pagination {
        bottom: 10px;
    }
    
    .products-swiper .swiper-pagination-bullet {
        background: #007bff;
        opacity: 0.3;
    }
    
    .products-swiper .swiper-pagination-bullet-active {
        opacity: 1;
    }
    
    /* Responsividade para carrossel */
    @media (max-width: 767.98px) {
        .carousel-container {
            padding: 0 85px; /* Mais espaço no tablet */
        }
        
        .carousel-nav-next,
        .carousel-nav-prev {
            width: 40px;
            height: 40px;
        }
        
        .carousel-nav-next i,
        .carousel-nav-prev i {
            font-size: 16px;
        }
        
        .carousel-nav-next {
            right: -20px;
        }
        
        .carousel-nav-prev {
            left: -20px;
        }
        
        .products-swiper {
            padding: 15px 5px 40px 5px;
        }
        
        .products-swiper .swiper-slide {
            width: 250px !important;
            max-width: 250px;
            padding: 0 4px;
        }
    }
    
    @media (max-width: 575.98px) {
        .carousel-container {
            padding: 0 75px; /* Mais espaço no mobile */
        }
        
        .carousel-nav-next,
        .carousel-nav-prev {
            width: 36px;
            height: 36px;
        }
        
        .carousel-nav-next i,
        .carousel-nav-prev i {
            font-size: 14px;
        }
        
        .carousel-nav-next {
            right: -18px;
        }
        
        .carousel-nav-prev {
            left: -18px;
        }
        
        .products-swiper {
            padding: 10px 3px 35px 3px;
        }
        
        .products-swiper .swiper-slide {
            width: 220px !important;
            max-width: 220px;
            padding: 0 3px;
        }
    }
    
    /* Container principal com espaço para as setas */
    .container-fluid {
        padding-left: 50px !important;
        padding-right: 50px !important;
    }
    
    @media (max-width: 767.98px) {
        .container-fluid {
            padding-left: 35px !important;
            padding-right: 35px !important;
        }
    }
    
    @media (max-width: 575.98px) {
        .container-fluid {
            padding-left: 30px !important;
            padding-right: 30px !important;
        }
    }

    /* Alinhamento perfeito dos cards */
    .product-card-wrapper .card {
        display: flex;
        flex-direction: column;
    }
    
    .product-card-wrapper .card-body {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    .product-card-wrapper .card-title {
        height: 2.4rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        line-height: 1.2;
    }

    /* Responsividade para carrossel */
    @media (max-width: 767.98px) {
        .products-swiper {
            padding: 0 30px 40px 30px;
        }
        
        .products-swiper .swiper-button-next,
        .products-swiper .swiper-button-prev {
            width: 30px;
            height: 30px;
            margin-top: -15px;
        }
        
        .products-swiper .swiper-button-next::after,
        .products-swiper .swiper-button-prev::after {
            font-size: 12px;
        }
        
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
    }
    
    @media (max-width: 575.98px) {
        .products-swiper {
            padding: 0 25px 35px 25px;
        }
    }
    
    @media (min-width: 768px) and (max-width: 991.98px) {
        .products-swiper {
            padding: 0 45px 45px 45px;
        }
    }
    
    @media (min-width: 1400px) {
        .products-swiper {
            padding: 0 60px 60px 60px;
        }
    }
</style>

<div class="container-fluid px-3">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-dark mb-3">🛍️ Nossa Loja</h1>
        <p class="text-muted mx-auto" style="max-width: 600px;">Confira produtos exclusivos relacionados ao nosso conteúdo — livros, camisetas, e muito mais!</p>
    </div>

    @if($featuredProducts->count() > 0)
    <div class="mb-5">
        <h2 class="h3 fw-bold text-dark mb-4 text-center">✨ Produtos em Destaque</h2>

        <div class="d-flex flex-wrap justify-content-center gap-3 product-grid-fixed">
            @foreach($featuredProducts as $product)
                <div class="product-card-wrapper">
                    @include('store.partials.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <section class="mb-5">
        <h2 class="h3 fw-bold text-dark mb-4 text-center">📦 Todos os Produtos</h2>

        @if($products->count() > 0)
        <div class="carousel-container">
            <!-- Botões de navegação fora do Swiper -->
            <div class="swiper-button-next carousel-nav-next">
                <i class="fas fa-chevron-right"></i>
            </div>
            <div class="swiper-button-prev carousel-nav-prev">
                <i class="fas fa-chevron-left"></i>
            </div>
            
            <!-- Swiper Container -->
            <div class="swiper products-swiper">
                <div class="swiper-wrapper">
                    @foreach($products as $product)
                        <div class="swiper-slide">
                            @include('store.partials.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <div class="text-muted mb-4" style="font-size: 4rem;">
                <i class="fas fa-box-open"></i>
            </div>
            <h3 class="h5 fw-semibold text-dark mb-2">Nenhum produto encontrado</h3>
            <p class="text-muted">Em breve teremos produtos disponíveis para você!</p>
        </div>
        @endif
    </section>
</div>

{{-- Carrinho lateral --}}
<div id="cart-sidebar" class="position-fixed top-0 end-0 h-100 bg-white shadow-lg translate-x-full transition-transform" style="width: 320px; z-index: 1050; transform: translateX(100%); transition: transform 0.3s ease;">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
        <h3 class="h5 fw-semibold text-dark mb-0">Carrinho</h3>
        <button id="close-cart" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div id="cart-content" class="p-3 flex-fill overflow-auto text-muted">
        <p class="text-center text-muted">Seu carrinho está vazio</p>
    </div>
    <div class="p-3 border-top bg-light">
        <a href="{{ route('store.cart') }}" class="btn btn-primary w-100">
            Ver Carrinho Completo
        </a>
    </div>
</div>

<div id="cart-overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none" style="z-index: 1040;"></div>
@endsection

@push('scripts')
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar se Swiper está disponível
        if (typeof Swiper === 'undefined') {
            console.error('Swiper não foi carregado!');
            return;
        }

        // Aguardar um pouco para garantir que tudo carregou
        setTimeout(() => {
            // Inicializar Swiper para produtos
            console.log('Inicializando Swiper...');
            const productsSwiper = new Swiper('.products-swiper', {
            slidesPerView: 'auto',
            spaceBetween: 30,
            loop: true,
            centeredSlides: false,
            grabCursor: true,
            watchOverflow: true, /* Evita problemas quando há poucos slides */
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            navigation: {
                nextEl: '.carousel-nav-next',
                prevEl: '.carousel-nav-prev',
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    centeredSlides: true,
                },
                576: {
                    slidesPerView: 2,
                    spaceBetween: 25,
                    centeredSlides: false,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
                992: {
                    slidesPerView: 4,
                    spaceBetween: 30,
                }
            },
            on: {
                init: function () {
                    console.log('Swiper inicializado com sucesso!');
                    // Garantir largura fixa nos slides
                    this.slides.forEach(slide => {
                        slide.style.width = '280px';
                        slide.style.flexShrink = '0';
                    });
                },
                resize: function() {
                    // Manter largura fixa ao redimensionar
                    this.slides.forEach(slide => {
                        slide.style.width = '280px';
                        slide.style.flexShrink = '0';
                    });
                }
            }
        });
        }, 1000); // Aguardar 100ms

        // Código existente do carrinho
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
        document.getElementById('cart-content').innerHTML = '<p class="text-center text-muted">Carregando...</p>';
    }

    function updateCartCounter(count) {
        const counter = document.querySelector('.cart-counter');
        if (counter) {
            counter.textContent = count;
            counter.classList.toggle('d-none', count === 0);
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
@endpush