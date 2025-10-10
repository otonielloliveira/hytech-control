@extends('layouts.blog')

@section('title', 'Carrinho de Compras')

@push('styles')
    <style>
        .cart-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            margin-top: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .product-image {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .quantity-input {
            width: 60px;
            text-align: center;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .product-image {
                width: 50px;
                height: 60px;
            }

            .quantity-input {
                width: 50px;
            }
        }
    </style>
@endpush

@section('content')

    <!-- Page Header -->
    @php
        $banners = App\Models\Banner::where('is_active', true)->orderBy('sort_order')->get();
    @endphp
    @if ($banners->count() > 0)
        <div class="blog-banner">
            <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach ($banners as $index => $banner)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                            class="{{ $index === 0 ? 'active' : '' }}"></button>
                    @endforeach
                </div>

                <div class="carousel-inner">
                    @foreach ($banners as $index => $banner)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}"
                            style="background-image: url('{{ $banner->image_url }}');">
                            <div class="carousel-overlay">
                                <div class="container">
                                    <div class="carousel-content">
                                        <h1>{{ $banner->title }}</h1>
                                        @if ($banner->subtitle)
                                            <h2>{{ $banner->subtitle }}</h2>
                                        @endif
                                        @if ($banner->description)
                                            <p>{{ $banner->description }}</p>
                                        @endif
                                        @if ($banner->link_url)
                                            <a href="{{ $banner->link_url }}" class="btn-hero"
                                                target="{{ $banner->target }}">
                                                {{ $banner->button_text }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($banners->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                @endif
            </div>
        </div>
    @endif

    <!-- Barra de Pesquisa e Login -->
    <section class="search-login-bar">
        <div class="container-fluid">
            <div class="row align-items-center py-3">
                <!-- Campo de Pesquisa -->
                <div class="col-lg-6 col-md-8 mb-2 mb-md-0">
                    <form action="{{ route('blog.search') }}" method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control search-input"
                                placeholder="🔍 Pesquisar posts, notícias, petições..." value="{{ request('q') }}"
                                autocomplete="off">
                            <button class="btn btn-search" type="submit">
                                <i class="fas fa-search"></i>
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Área de Login/Cadastro -->
                <div class="col-lg-6 col-md-4 text-end">
                    <div class="auth-buttons">
                        @auth('client')
                            <!-- Cliente logado -->
                            <div class="dropdown">
                                <a href="#" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i>
                                    Olá, {{ auth('client')->user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('client.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>Meu Painel
                                        </a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.profile') }}">
                                            <i class="fas fa-user-edit me-2"></i>Meu Perfil
                                        </a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.addresses') }}">
                                            <i class="fas fa-map-marker-alt me-2"></i>Endereços
                                        </a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.preferences') }}">
                                            <i class="fas fa-cog me-2"></i>Preferências
                                        </a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('client.logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <!-- Cliente não logado -->
                            <a href="#" class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal"
                                data-bs-target="#loginModal">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Entrar
                            </a>
                            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#registerModal">
                                <i class="fas fa-user-plus me-1"></i>
                                Cadastrar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="container my-4">
        <div class="cart-container">
            <!-- Header -->
            <!-- Título do carrinho -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0">🛒 Seu Carrinho</h2>
                <span class="badge bg-secondary">{{ count($cartItems) }}
                    {{ count($cartItems) === 1 ? 'item' : 'itens' }}</span>
            </div>

            @if (count($cartItems) > 0)
                <!-- Cart Table -->
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Produto(s)</th>
                                <th>Quantidade</th>
                                <th>Entrega</th>
                                <th>Valor Unitário</th>
                                <th>Valor Total</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $itemId => $item)
                                <tr data-item-id="{{ $itemId }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item['product_image'] }}" alt="{{ $item['product_name'] }}"
                                                class="rounded me-3"
                                                style="width: 60px; height: 80px; object-fit: cover; border: 1px solid #dee2e6;">
                                            <div>
                                                <strong>{{ $item['product_name'] }}</strong><br>
                                                <small
                                                    class="text-muted">{{ $item['product_sku'] ?? 'Necessidade Pública' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm w-50"
                                            value="{{ $item['quantity'] }}" min="1"
                                            onchange="updateQuantity('{{ $itemId }}', this.value)">
                                    </td>
                                    <td>R$ {{ number_format(16.9, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($item['product_price'], 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</td>
                                    <td>
                                        <button onclick="removeItem('{{ $itemId }}')"
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
        </div>

        <!-- Summary Box -->
        <!-- Resumo do Pedido -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Resumo do Pedido</h5>

                @php
                    $subtotal = 0;
                    foreach ($cartItems as $item) {
                        $subtotal += $item['subtotal'];
                    }
                    $shipping = 16.9;
                    $total = $subtotal + $shipping;
                @endphp

                <div class="d-flex justify-content-between mb-2">
                    <span>1 Produto:</span>
                    <strong>R$ {{ number_format($subtotal, 2, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Frete:</span>
                    <strong>R$ {{ number_format($shipping, 2, ',', '.') }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Valor Total:</span>
                    <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('store.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-plus me-2"></i>Escolher + produtos
            </a>
            <a href="{{ route('store.checkout') }}" class="btn btn-primary">
                <i class="fas fa-shopping-cart me-2"></i>Comprar
            </a>
        </div>

        <!-- Info Box -->
        <div class="mt-4 p-3 bg-light rounded">
            <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Informações Importantes</h6>
            <ul class="mb-0 ps-4">
                <li>Atenção: Atualmente o frete é fixo de acordo com a quantidade de itens do carrinho.</li>
                <li>Atenção: O prazo de entrega começa a contar a partir da aprovação do pagamento.</li>
            </ul>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <div class="fs-1 mb-3">🛒</div>
            <h2 class="fw-bold mb-3">Ops! Seu carrinho está vazio</h2>
            <p class="text-muted mb-4">Que tal explorar nossa seleção de produtos?</p>
            <a href="{{ route('store.index') }}" class="btn-primary">
                Descobrir Produtos Incríveis
            </a>
        </div>
        @endif
    </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function updateQuantity(itemId, newQty) {
            if (newQty < 1) return;

            Swal.fire({
                title: 'Atualizando...',
                text: 'Processando alteração',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Simulação de atualização (substitua pela chamada AJAX real)
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Quantidade atualizada!',
                    timer: 1500,
                    showConfirmButton: false
                });
            }, 800);
        }

        function removeItem(itemId) {
            Swal.fire({
                title: 'Remover item?',
                text: 'Tem certeza que deseja remover este produto?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, remover!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector(`[data-item-id="${itemId}"]`).remove();
                    Swal.fire({
                        icon: 'success',
                        title: 'Item removido!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    </script>
@endpush
