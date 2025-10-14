<div class="card h-100 product-card border-0 shadow-sm d-flex flex-column">
    {{-- Imagem --}}
    <a href="{{ route('store.product', $product->slug) }}" class="text-decoration-none">
        <div class="position-relative bg-light" style="aspect-ratio: 1; overflow: hidden;">
            @if($product->images && count($product->images) > 0)
                <img src="{{ Storage::url($product->images[0]) }}"
                     alt="{{ $product->name }}"
                     class="w-100 h-100 object-fit-cover position-absolute top-0 start-0">
            @else
                <div class="d-flex align-items-center justify-content-center h-100">
                    <i class="fas fa-box text-muted" style="font-size: 2rem;"></i>
                </div>
            @endif
        </div>
    </a>

    {{-- Info --}}
    <div class="card-body p-2 text-center d-flex flex-column flex-grow-1">
        <a href="{{ route('store.product', $product->slug) }}" class="text-decoration-none">
            <h3 class="card-title fw-medium text-dark mb-2" style="font-size: 0.8rem; line-height: 1.2; height: 2.4rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Preço --}}
        <div class="mb-2 flex-grow-1 d-flex flex-column justify-content-center">
            @if($product->isOnSale())
                <p class="fw-bold text-danger mb-0" style="font-size: 0.9rem;">
                    R$ {{ number_format($product->sale_price, 2, ',', '.') }}
                </p>
                <p class="text-muted text-decoration-line-through mb-0" style="font-size: 0.75rem;">
                    R$ {{ number_format($product->price, 2, ',', '.') }}
                </p>
            @else
                <p class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">
                    R$ {{ number_format($product->price, 2, ',', '.') }}
                </p>
            @endif
        </div>

        {{-- Botão --}}
        <div class="d-grid mt-auto">
            @if($product->in_stock)
                <button class="btn btn-primary btn-sm add-to-cart" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"
                        data-product-id="{{ $product->id }}">
                    <i class="fas fa-shopping-cart me-1" style="font-size: 0.6rem;"></i>
                    Adicionar
                </button>
            @else
                <button class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;" disabled>
                    <i class="fas fa-times me-1" style="font-size: 0.6rem;"></i>
                    Indisponível
                </button>
            @endif
        </div>
    </div>
</div>
