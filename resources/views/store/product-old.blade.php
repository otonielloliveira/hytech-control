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
                    @if($product->images && count($product->images) > 0)
                        <img id="main-image" src="{{ Storage::url($product->images[0]) }}" 
                             alt="{{ $product->name }}" 
                             class="w-100 rounded-3" 
                             style="height: 400px; object-fit: cover;">
                        
                        <!-- Zoom Icon -->
                        <button class="btn btn-light btn-sm position-absolute top-0 end-0 m-3 rounded-circle" 
                                data-bs-toggle="modal" data-bs-target="#imageModal">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        
                        <!-- Sale Badge -->
                        @if($product->isOnSale())
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
                @if($product->images && count($product->images) > 1)
                    <div class="row g-2">
                        @foreach($product->images as $index => $image)
                            <div class="col-3">
                                <button class="btn p-0 w-100 thumbnail-btn {{ $index === 0 ? 'active' : '' }}" 
                                        onclick="changeMainImage('{{ Storage::url($image) }}', this)">
                                    <img src="{{ Storage::url($image) }}" 
                                         alt="{{ $product->name }} - {{ $index + 1 }}" 
                                         class="w-100 rounded-2"
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
                            @for($i = 1; $i <= 5; $i++)
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
                    @if($product->isOnSale())
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
                            @if($product->in_stock)
                                @if($product->manage_stock)
                                    @if($product->stock_quantity <= 5)
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
                </div>            <!-- Product Info -->
            <div class="p-8 flex flex-col justify-between">
                <div class="space-y-6">
                    <!-- Header -->
                    <div class="border-b pb-6">
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">{{ $product->name }}</h1>
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">SKU: {{ $product->sku }}</p>
                            <div class="flex items-center space-x-2">
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-sm"></i>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-500">(4.8)</span>
                            </div>
                        </div>
                    </div>

                    @if($product->short_description)
                        <div class="prose prose-gray">
                            <p class="text-lg text-gray-600 leading-relaxed">{{ $product->short_description }}</p>
                        </div>
                    @endif

                    <!-- Price -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6">
                        @if($product->isOnSale())
                            <div class="space-y-2">
                                <div class="flex items-center space-x-4">
                                    <span class="text-4xl font-bold text-red-600">
                                        R$ {{ number_format($product->sale_price, 2, ',', '.') }}
                                    </span>
                                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                        -{{ $product->getDiscountPercentage() }}%
                                    </span>
                                </div>
                                <span class="text-lg text-gray-500 line-through block">
                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                </span>
                            </div>
                        @else
                            <span class="text-4xl font-bold text-gray-900">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    <!-- Stock Status -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        @if($product->in_stock)
                            @if($product->manage_stock)
                                @if($product->stock_quantity <= 5)
                                    <div class="flex items-center text-orange-600">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <span class="font-medium">Apenas {{ $product->stock_quantity }} unidade(s) disponível(eis)</span>
                                    </div>
                                @else
                                    <div class="flex items-center text-green-600">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        <span class="font-medium">Em estoque ({{ $product->stock_quantity }} disponíveis)</span>
                                    </div>
                                @endif
                            @else
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span class="font-medium">Em estoque</span>
                                </div>
                            @endif
                        @else
                            <div class="flex items-center text-red-600">
                                <i class="fas fa-times-circle mr-2"></i>
                                <span class="font-medium">Fora de estoque</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Add to Cart Section -->
                <div class="pt-6 border-t">
                    @if($product->in_stock)
                        <form id="add-to-cart-form" class="space-y-6">
                            <div class="flex items-center justify-between">
                                <label for="quantity" class="text-sm font-semibold text-gray-700">Quantidade:</label>
                                <div class="flex items-center bg-white border-2 border-gray-200 rounded-lg overflow-hidden">
                                    <button type="button" onclick="changeQuantity(-1)" 
                                            class="px-4 py-2 hover:bg-gray-100 transition-colors border-r border-gray-200">
                                        <i class="fas fa-minus text-sm"></i>
                                    </button>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" 
                                           max="{{ $product->manage_stock ? $product->stock_quantity : 999 }}" 
                                           class="w-20 text-center border-0 focus:ring-0 py-2 font-semibold">
                                    <button type="button" onclick="changeQuantity(1)" 
                                            class="px-4 py-2 hover:bg-gray-100 transition-colors border-l border-gray-200">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <button type="submit" 
                                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="fas fa-shopping-cart mr-3"></i>
                                    Adicionar ao Carrinho
                                </button>
                                <a href="{{ route('store.cart') }}" 
                                   class="w-full block text-center bg-gray-100 text-gray-700 px-8 py-3 rounded-xl hover:bg-gray-200 transition-colors font-semibold">
                                    <i class="fas fa-eye mr-2"></i>
                                    Ver Carrinho
                                </a>
                            </div>
                        </form>
                    @else
                        <button class="w-full bg-gray-300 text-gray-500 px-8 py-4 rounded-xl cursor-not-allowed font-bold text-lg" disabled>
                            <i class="fas fa-times mr-3"></i>
                            Produto Indisponível
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

                <!-- Purchase Options -->
                @if($product->in_stock)
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
            
            @if($product->short_description)
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
@if($product->description || $product->weight || $product->length || $product->width || $product->height)
    <div class="container my-5">
        <div class="bg-white rounded-3 shadow-sm overflow-hidden">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs nav-fill border-0 bg-light" id="productTabs" role="tablist">
                @if($product->description)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="description-tab" data-bs-toggle="tab" 
                                data-bs-target="#description" type="button" role="tab">
                            <i class="fas fa-info-circle me-2"></i>Descrição
                        </button>
                    </li>
                @endif
                @if($product->weight || $product->length || $product->width || $product->height)
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
                @if($product->description)
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

                @if($product->weight || $product->length || $product->width || $product->height)
                    <div class="tab-pane fade" id="specifications" role="tabpanel">
                        <h5 class="fw-bold mb-4">Especificações Técnicas</h5>
                        <div class="row g-3">
                            @if($product->weight)
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
                            @if($product->length && $product->width && $product->height)
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-ruler text-primary fa-2x mb-3"></i>
                                            <h6 class="card-title">Dimensões</h6>
                                            <p class="card-text fw-bold">{{ $product->length }}x{{ $product->width }}x{{ $product->height }}cm</p>
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
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star"></i>
                                    @endfor
                                </div>
                                <p class="mb-0 text-muted">127 avaliações</p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <!-- Rating Breakdown -->
                            <div class="mb-4">
                                @for($i = 5; $i >= 1; $i--)
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="me-2">{{ $i }} estrelas</span>
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-warning" style="width: {{ rand(20, 80) }}%"></div>
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
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <small class="text-muted">15 out 2025</small>
                                </div>
                                <p class="mb-0">Produto de excelente qualidade! A camiseta é muito confortável e o tecido é macio. Recomendo!</p>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">Maria Santos</h6>
                                        <div class="text-warning small">
                                            @for($i = 1; $i <= 4; $i++)
                                                <i class="fas fa-star"></i>
                                            @endfor
                                            <i class="far fa-star"></i>
                                        </div>
                                    </div>
                                    <small class="text-muted">12 out 2025</small>
                                </div>
                                <p class="mb-0">Boa camiseta, chegou no prazo. Única observação é que veio um pouco apertada do que esperava.</p>
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
    @if($relatedProducts->count() > 0)
        <div class="border-t pt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Produtos Relacionados</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    @include('store.partials.product-card', ['product' => $relatedProduct])
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function changeMainImage(imageSrc, thumbnail) {
    document.getElementById('main-image').src = imageSrc;
    
    // Update thumbnail selection
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('ring-2', 'ring-blue-500');
    });
    thumbnail.classList.add('ring-2', 'ring-blue-500');
}

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

// Add to cart form submission
document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const quantity = document.getElementById('quantity').value;
    const button = this.querySelector('button[type="submit"]');
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adicionando...';
    
    fetch(`/loja/carrinho/adicionar/{{ $product->id }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ quantity: parseInt(quantity) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            button.innerHTML = '<i class="fas fa-check mr-2"></i>Adicionado!';
            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            button.classList.add('bg-green-600');
            
            // Update cart counter
            updateCartCounter(data.cart_totals.items_count);
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600');
                button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                button.disabled = false;
            }, 2000);
        } else {
            alert(data.message);
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        alert('Erro ao adicionar produto ao carrinho');
        button.innerHTML = originalText;
        button.disabled = false;
    });
});

function updateCartCounter(count) {
    const counter = document.querySelector('.cart-counter');
    if (counter) {
        counter.textContent = count;
        counter.classList.toggle('hidden', count === 0);
    }
}
</script>
@endpush
@endsection