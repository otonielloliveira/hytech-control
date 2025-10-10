<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
    <div class="relative">
        <a href="{{ route('store.product.show', $product->sku) }}">
            <div class="aspect-square bg-gray-200 overflow-hidden">
                @if($product->images && count($product->images) > 0)
                    <img src="{{ Storage::url($product->images[0]) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <i class="fas fa-image text-4xl"></i>
                    </div>
                @endif
            </div>
        </a>
        
        @if($product->featured)
            <div class="absolute top-2 left-2">
                <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                    <i class="fas fa-star mr-1"></i>Destaque
                </span>
            </div>
        @endif
        
        @if($product->isOnSale())
            <div class="absolute top-2 right-2">
                <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                    -{{ $product->getDiscountPercentage() }}%
                </span>
            </div>
        @endif
        
        @if(!$product->in_stock)
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <span class="bg-gray-800 text-white px-3 py-2 rounded-lg font-semibold">
                    Esgotado
                </span>
            </div>
        @endif
    </div>
    
    <div class="p-4">
        <a href="{{ route('store.product.show', $product->sku) }}" class="block">
            <h3 class="font-semibold text-gray-900 mb-2 hover:text-blue-600 transition-colors">
                {{ $product->name }}
            </h3>
        </a>
        
        @if($product->short_description)
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                {{ $product->short_description }}
            </p>
        @endif
        
        <div class="flex items-center justify-between mb-4">
            <div class="flex flex-col">
                @if($product->isOnSale())
                    <span class="text-lg font-bold text-red-600">
                        R$ {{ number_format($product->sale_price, 2, ',', '.') }}
                    </span>
                    <span class="text-sm text-gray-500 line-through">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </span>
                @else
                    <span class="text-lg font-bold text-gray-900">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </span>
                @endif
            </div>
            
            @if($product->manage_stock && $product->stock_quantity <= 5 && $product->in_stock)
                <span class="text-xs text-orange-600 font-medium">
                    Últimas {{ $product->stock_quantity }} unidades
                </span>
            @endif
        </div>
        
        @if($product->in_stock)
            <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors add-to-cart"
                    data-product-id="{{ $product->id }}">
                <i class="fas fa-shopping-cart mr-2"></i>
                Adicionar ao Carrinho
            </button>
        @else
            <button class="w-full bg-gray-400 text-white py-2 px-4 rounded-lg cursor-not-allowed" disabled>
                <i class="fas fa-times mr-2"></i>
                Indisponível
            </button>
        @endif
    </div>
</div>