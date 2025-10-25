@extends('layouts.blog')

@section('title', $product->name . ' - Loja')

@section('content')
    <div class="container-fluid bg-light py-3">
        <!-- Breadcrumb -->
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('blog.index') }}" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i>Início
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('store.index') }}" class="text-decoration-none">Loja</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container my-4">
        <!-- Product Main Section -->
        <div class="row g-4">
            <div class="col-lg-5">
                <!-- Product Images -->
                <div class="bg-white rounded-3 shadow-sm p-3 mb-3 position-sticky" style="top: 20px;">
                    <!-- Main Image -->
                    <div class="position-relative mb-3">
                        @if ($product->images && count($product->images) > 0)
                            <img id="main-image" src="{{ Storage::url($product->images[0]) }}" alt="{{ $product->name }}"
                                class="w-100 rounded-3" style="height: 400px; object-fit: cover;">

                            <!-- Zoom Icon -->
                            <button class="btn btn-light btn-sm position-absolute top-0 end-0 m-3 rounded-circle"
                                data-bs-toggle="modal" data-bs-target="#imageModal">
                                <i class="fas fa-search-plus"></i>
                            </button>

                            <!-- Sale Badge -->
                            @if ($product->isOnSale())
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-danger fs-6 px-3 py-2">
                                        -{{ $product->getDiscountPercentage() }}%
                                    </span>
                                </div>
                            @endif
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light rounded-3"
                                style="height: 400px;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-image fa-4x mb-3"></i>
                                    <p>Imagem não disponível</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Thumbnail Images -->
                    @if ($product->images && count($product->images) > 1)
                        <div class="row g-2">
                            @foreach ($product->images as $index => $image)
                                <div class="col-3">
                                    <button class="btn p-0 w-100 thumbnail-btn {{ $index === 0 ? 'active' : '' }}"
                                        onclick="changeMainImage('{{ Storage::url($image) }}', this)">
                                        <img src="{{ Storage::url($image) }}"
                                            alt="{{ $product->name }} - {{ $index + 1 }}" class="w-100 rounded-2"
                                            style="height: 80px; object-fit: cover;">
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-7">
                <!-- Product Info -->
                <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                    <!-- Product Title & Rating -->
                    <div class="mb-3">
                        <h1 class="h3 fw-bold mb-2">{{ $product->name }}</h1>
                        <div class="d-flex align-items-center mb-2">
                            <div class="text-warning me-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= 4 ? '' : '-o' }}"></i>
                                @endfor
                            </div>
                            <span class="text-muted small">(4.8) | 127 avaliações</span>
                            <span class="text-muted mx-2">|</span>
                            <span class="text-success small">
                                <i class="fas fa-truck me-1"></i>Frete grátis
                            </span>
                        </div>
                        <div class="text-muted small">
                            <span class="badge bg-light text-dark">SKU: {{ $product->sku }}</span>
                        </div>
                    </div>

                    <!-- Price Section -->
                    <div class="bg-light rounded-3 p-3 mb-4">
                        @if ($product->isOnSale())
                            <div class="d-flex align-items-center mb-2">
                                <h2 class="h2 fw-bold text-danger mb-0 me-3">
                                    R$ {{ number_format($product->sale_price, 2, ',', '.') }}
                                </h2>
                                <span class="badge bg-danger">-{{ $product->getDiscountPercentage() }}%</span>
                            </div>
                            <div class="text-muted">
                                <span class="text-decoration-line-through">
                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                </span>
                            </div>
                        @else
                            <h2 class="h2 fw-bold text-primary mb-0">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                            </h2>
                        @endif
                        <small class="text-muted">Preço à vista no PIX com 5% de desconto</small>
                    </div>

                    <!-- Stock & Shipping -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded-2 p-3 h-100">
                                <h6 class="fw-semibold mb-2">
                                    <i class="fas fa-box text-primary me-2"></i>Estoque
                                </h6>
                                @if ($product->in_stock)
                                    @if ($product->manage_stock)
                                        @if ($product->stock_quantity <= 5)
                                            <span class="text-warning">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Últimas {{ $product->stock_quantity }} unidades
                                            </span>
                                        @else
                                            <span class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                {{ $product->stock_quantity }} disponíveis
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Disponível
                                        </span>
                                    @endif
                                @else
                                    <span class="text-danger">
                                        <i class="fas fa-times-circle me-1"></i>
                                        Indisponível
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-2 p-3 h-100">
                                <h6 class="fw-semibold mb-2">
                                    <i class="fas fa-shipping-fast text-success me-2"></i>Entrega
                                </h6>
                                <p class="mb-1 small">Frete grátis para todo Brasil</p>
                                <p class="mb-0 small text-muted">Entrega em 5-7 dias úteis</p>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Options -->
                    @if ($product->in_stock)
                        <form id="add-to-cart-form">
                            <!-- Quantity Selector -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Quantidade:</label>
                                <div class="input-group" style="width: 140px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(-1)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1"
                                        max="{{ $product->manage_stock ? $product->stock_quantity : 999 }}"
                                        class="form-control text-center">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(1)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-danger btn-lg fw-semibold">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    Adicionar ao Carrinho
                                </button>
                                <button type="button" class="btn btn-warning btn-lg fw-semibold">
                                    <i class="fas fa-bolt me-2"></i>
                                    Comprar Agora
                                </button>
                            </div>

                            <!-- Secondary Actions -->
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-heart me-1"></i>
                                        Favoritar
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-share-alt me-1"></i>
                                        Compartilhar
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="d-grid">
                            <button class="btn btn-secondary btn-lg disabled">
                                <i class="fas fa-times me-2"></i>
                                Produto Indisponível
                            </button>
                        </div>
                    @endif

                    <!-- Trust Badges -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="text-success">
                                    <i class="fas fa-shield-alt fa-2x mb-1"></i>
                                    <p class="small mb-0">Compra Segura</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-primary">
                                    <i class="fas fa-undo fa-2x mb-1"></i>
                                    <p class="small mb-0">Troca Grátis</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-warning">
                                    <i class="fas fa-star fa-2x mb-1"></i>
                                    <p class="small mb-0">Qualidade</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($product->short_description)
                    <!-- Short Description -->
                    <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                        <h5 class="fw-bold mb-3">Sobre este produto</h5>
                        <p class="text-muted mb-0">{{ $product->short_description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Product Details Tabs -->
    @if ($product->description || $product->weight || $product->length || $product->width || $product->height)
        <div class="container my-5">
            <div class="bg-white rounded-3 shadow-sm overflow-hidden">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs nav-fill border-0 bg-light" id="productTabs" role="tablist">
                    @if ($product->description)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-semibold" id="description-tab" data-bs-toggle="tab"
                                data-bs-target="#description" type="button" role="tab">
                                <i class="fas fa-info-circle me-2"></i>Descrição
                            </button>
                        </li>
                    @endif
                    @if ($product->weight || $product->length || $product->width || $product->height)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold" id="specifications-tab" data-bs-toggle="tab"
                                data-bs-target="#specifications" type="button" role="tab">
                                <i class="fas fa-cogs me-2"></i>Especificações
                            </button>
                        </li>
                    @endif
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="reviews-tab" data-bs-toggle="tab"
                            data-bs-target="#reviews" type="button" role="tab">
                            <i class="fas fa-star me-2"></i>Avaliações (127)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="shipping-tab" data-bs-toggle="tab"
                            data-bs-target="#shipping" type="button" role="tab">
                            <i class="fas fa-truck me-2"></i>Entrega
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content p-4" id="productTabsContent">
                    @if ($product->description)
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <h5 class="fw-bold mb-4">Descrição do Produto</h5>
                            <div class="mb-4">
                                {!! $product->description !!}
                            </div>

                            <!-- Características -->
                            <h6 class="fw-bold mb-3">Características:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Malha 301 penteada
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Gola careca
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Manga curta
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Estampa em silk screen
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($product->weight || $product->length || $product->width || $product->height)
                        <div class="tab-pane fade" id="specifications" role="tabpanel">
                            <h5 class="fw-bold mb-4">Especificações Técnicas</h5>
                            <div class="row g-3">
                                @if ($product->weight)
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-weight text-primary fa-2x mb-3"></i>
                                                <h6 class="card-title">Peso</h6>
                                                <p class="card-text fw-bold">{{ $product->weight }}kg</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($product->length && $product->width && $product->height)
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-ruler text-primary fa-2x mb-3"></i>
                                                <h6 class="card-title">Dimensões</h6>
                                                <p class="card-text fw-bold">
                                                    {{ $product->length }}x{{ $product->width }}x{{ $product->height }}cm
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-4 bg-light rounded-3">
                                    <h2 class="display-4 fw-bold text-warning mb-0">4.8</h2>
                                    <div class="text-warning mb-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star"></i>
                                        @endfor
                                    </div>
                                    <p class="mb-0 text-muted">127 avaliações</p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <!-- Rating Breakdown -->
                                <div class="mb-4">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-2">{{ $i }} estrelas</span>
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" style="width: {{ rand(20, 80) }}%">
                                                </div>
                                            </div>
                                            <span class="text-muted small">{{ rand(10, 50) }}</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Sample Reviews -->
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Avaliações dos clientes</h6>

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">João Silva</h6>
                                            <div class="text-warning small">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <small class="text-muted">15 out 2025</small>
                                    </div>
                                    <p class="mb-0">Produto de excelente qualidade! A camiseta é muito confortável e o
                                        tecido é macio. Recomendo!</p>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">Maria Santos</h6>
                                            <div class="text-warning small">
                                                @for ($i = 1; $i <= 4; $i++)
                                                    <i class="fas fa-star"></i>
                                                @endfor
                                                <i class="far fa-star"></i>
                                            </div>
                                        </div>
                                        <small class="text-muted">12 out 2025</small>
                                    </div>
                                    <p class="mb-0">Boa camiseta, chegou no prazo. Única observação é que veio um pouco
                                        apertada do que esperava.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="shipping" role="tabpanel">
                        <h5 class="fw-bold mb-4">Informações de Entrega</h5>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-shipping-fast text-success fa-3x mb-3"></i>
                                        <h6 class="card-title">Frete Grátis</h6>
                                        <p class="card-text">Para compras acima de R$ 99,00</p>
                                        <small class="text-muted">Válido para todo o Brasil</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <i class="fas fa-clock text-info fa-3x mb-3"></i>
                                        <h6 class="card-title">Entrega Rápida</h6>
                                        <p class="card-text">5-7 dias úteis</p>
                                        <small class="text-muted">Após confirmação do pagamento</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Calcular Frete</h6>
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">CEP:</label>
                                    <input type="text" class="form-control" placeholder="00000-000">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary">Calcular</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Related Products -->
    @if ($relatedProducts->count() > 0)
        <div class="container my-5">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="fw-bold mb-0">
                        <i class="fas fa-heart text-danger me-2"></i>
                        Produtos Relacionados
                    </h4>
                    <a href="{{ route('store.index') }}" class="btn btn-outline-primary btn-sm">
                        Ver todos <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="row g-3">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            @include('store.partials.product-card', ['product' => $relatedProduct])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Image Modal -->
    @if ($product->images && count($product->images) > 0)
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="imageModalLabel">{{ $product->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <img id="modal-image" src="{{ Storage::url($product->images[0]) }}" alt="{{ $product->name }}"
                            class="w-100">
                    </div>
                </div>
            </div>
        </div>
    @endif

@section('styles')
    <style>
        .thumbnail-btn {
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .thumbnail-btn:hover,
        .thumbnail-btn.active {
            border-color: #dc3545;
            transform: scale(1.05);
        }

        .nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            color: #6c757d;
            background: transparent;
        }

        .nav-tabs .nav-link.active {
            background: #fff;
            color: #495057;
            border-bottom: 3px solid #dc3545;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            background: rgba(220, 53, 69, 0.1);
        }

        /* Custom button hover effects */
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }

        /* Loading animation for add to cart */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Image gallery functionality
        function changeMainImage(imageSrc, thumbnail) {
            document.getElementById('main-image').src = imageSrc;
            document.getElementById('modal-image').src = imageSrc;

            // Update thumbnail selection
            document.querySelectorAll('.thumbnail-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            thumbnail.classList.add('active');
        }

        // Quantity controls
        function changeQuantity(delta) {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);
            const minValue = parseInt(quantityInput.min);
            const maxValue = parseInt(quantityInput.max);
            const newValue = currentValue + delta;

            if (newValue >= minValue && newValue <= maxValue) {
                quantityInput.value = newValue;
            }
        }

        // Add to cart functionality
        document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const quantity = document.getElementById('quantity').value;
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;

            // Loading state
            button.disabled = true;
            button.classList.add('btn-loading');
            button.innerHTML = '<span class="me-2">Adicionando...</span>';

            fetch(`/loja/carrinho/adicionar/{{ $product->id }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        quantity: parseInt(quantity)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success state
                        button.classList.remove('btn-loading');
                        button.classList.remove('btn-danger');
                        button.classList.add('btn-success');
                        button.innerHTML = '<i class="fas fa-check me-2"></i>Adicionado!';

                        // Update cart counter
                        updateCartCounter(data.cart_totals.items_count);

                        // Show success toast
                        showToast('Produto adicionado ao carrinho!', 'success');

                        // Reset button after 3 seconds
                        setTimeout(() => {
                            button.innerHTML = originalText;
                            button.classList.remove('btn-success');
                            button.classList.add('btn-danger');
                            button.disabled = false;
                        }, 3000);
                    } else {
                        // Error state
                        button.classList.remove('btn-loading');
                        button.innerHTML = originalText;
                        button.disabled = false;
                        showToast(data.message || 'Erro ao adicionar produto', 'error');
                    }
                })
                .catch(error => {
                    // Error state
                    button.classList.remove('btn-loading');
                    button.innerHTML = originalText;
                    button.disabled = false;
                    showToast('Erro ao adicionar produto ao carrinho', 'error');
                });
        });

        // Update cart counter
        function updateCartCounter(count) {
            const counter = document.querySelector('.cart-counter');
            if (counter) {
                counter.textContent = count;
                counter.classList.toggle('d-none', count === 0);

                // Animate counter
                counter.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    counter.style.transform = 'scale(1)';
                }, 200);
            }
        }

        // Toast notification system
        function showToast(message, type = 'info') {
            // Create toast container if it doesn't exist
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }

            // Create toast
            const toastId = 'toast-' + Date.now();
            const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

            toastContainer.insertAdjacentHTML('beforeend', toastHtml);

            // Show toast
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                delay: 4000
            });
            toast.show();

            // Remove toast element after it's hidden
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }

        // Initialize tooltips and smooth scrolling
        document.addEventListener('DOMContentLoaded', function() {
            // Enable Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
