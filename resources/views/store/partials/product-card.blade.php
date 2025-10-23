<div class="product-card">
    <!-- Imagem do Produto -->
    <a href="{{ route('store.product', $product->slug) }}" class="text-decoration-none">
        <div class="product-image-container">
            @if($product->images && count($product->images) > 0)
                <img src="{{ Storage::url($product->images[0]) }}"
                     alt="{{ $product->name }}"
                     class="product-image">
            @else
                <div class="d-flex align-items-center justify-content-center" style="aspect-ratio: 1; background: #f5f5f5;">
                    <i class="fas fa-box text-muted" style="font-size: 3rem;"></i>
                </div>
            @endif

            @if($product->isOnSale())
                @php
                    $discount = round((($product->price - $product->sale_price) / $product->price) * 100);
                @endphp
                <span class="discount-badge">{{ $discount }}% OFF</span>
            @endif
        </div>
    </a>

    <!-- Informações do Produto -->
    <div class="p-3">
        <!-- Nome do Produto -->
        <a href="{{ route('store.product', $product->slug) }}" class="text-decoration-none">
            <h3 class="mb-2" style="font-size: 14px; font-weight: 400; color: #333; line-height: 1.3; height: 2.6em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                {{ $product->name }}
            </h3>
        </a>

        <!-- Preços -->
        <div class="mb-2">
            @if($product->isOnSale())
                <p class="price-original mb-1">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                <p class="price-current mb-1">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</p>
                <p class="installments">em até 10x de R$ {{ number_format($product->sale_price / 10, 2, ',', '.') }}</p>
            @else
                <p class="price-original mb-1" style="visibility: hidden;">R$ 0,00</p>
                <p class="price-current mb-1">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                <p class="installments">em até 10x de R$ {{ number_format($product->price / 10, 2, ',', '.') }}</p>
            @endif
        </div>

        <!-- Frete Grátis -->
        <div style="min-height: 21px; margin-bottom: 0.5rem;">
            @if($product->price > 100)
                <p class="free-shipping mb-0">
                    <i class="fas fa-truck me-1"></i>Frete grátis
                </p>
            @endif
        </div>

        <!-- Botão -->
        @if($product->in_stock)
            <button class="btn-add-cart add-to-cart" data-product-id="{{ $product->id }}">
                <i class="fas fa-shopping-cart me-2"></i>Adicionar ao carrinho
            </button>
        @else
            <button class="btn-add-cart" disabled>
                Indisponível
            </button>
        @endif
    </div>
</div>
