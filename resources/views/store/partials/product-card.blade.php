<div class="bg-white rounded-md border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden product-card">
    {{-- Imagem --}}
    <a href="{{ route('store.product', $product->slug) }}" class="block">
        <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
            @if($product->images && count($product->images) > 0)
                <img src="{{ Storage::url($product->images[0]) }}"
                     alt="{{ $product->name }}"
                     class="object-cover w-full h-full hover:scale-105 transition-transform duration-300">
            @else
                <i class="fas fa-box text-gray-400 text-3xl"></i>
            @endif
        </div>
    </a>

    {{-- Info --}}
    <div class="p-2 text-center">
        <a href="{{ route('store.product', $product->slug) }}">
            <h3 class="text-[13px] font-medium text-gray-800 hover:text-blue-600 transition-colors line-clamp-2">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Preço --}}
        <div class="mt-1">
            @if($product->isOnSale())
                <p class="text-sm font-bold text-red-600">
                    R$ {{ number_format($product->sale_price, 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 line-through">
                    R$ {{ number_format($product->price, 2, ',', '.') }}
                </p>
            @else
                <p class="text-sm font-semibold text-gray-900">
                    R$ {{ number_format($product->price, 2, ',', '.') }}
                </p>
            @endif
        </div>

        {{-- Botão --}}
        <div class="mt-2">
            @if($product->in_stock)
                <button class="w-full bg-orange-500 hover:bg-orange-600 text-white text-[11px] py-1 rounded-md transition-colors add-to-cart"
                        data-product-id="{{ $product->id }}">
                    <i class="fas fa-shopping-cart mr-1 text-[10px]"></i>
                    Adicionar
                </button>
            @else
                <button class="w-full bg-gray-300 text-gray-600 text-[11px] py-1 rounded-md cursor-not-allowed" disabled>
                    Indisponível
                </button>
            @endif
        </div>
    </div>
</div>
