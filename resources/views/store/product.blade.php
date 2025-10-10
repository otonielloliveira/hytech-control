@extends('layouts.blog')

@section('title', $product->name . ' - Loja')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('blog.index') }}" class="hover:text-blue-600">Início</a></li>
            <li><i class="fas fa-chevron-right text-gray-400"></i></li>
            <li><a href="{{ route('store.index') }}" class="hover:text-blue-600">Loja</a></li>
            <li><i class="fas fa-chevron-right text-gray-400"></i></li>
            <li class="text-gray-900 font-medium">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Product Images -->
        <div class="space-y-4">
            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                @if($product->images && count($product->images) > 0)
                    <img id="main-image" src="{{ Storage::url($product->images[0]) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <i class="fas fa-image text-6xl"></i>
                    </div>
                @endif
            </div>
            
            @if($product->images && count($product->images) > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->images as $index => $image)
                        <button class="aspect-square bg-gray-100 rounded-lg overflow-hidden thumbnail {{ $index === 0 ? 'ring-2 ring-blue-500' : '' }}" 
                                onclick="changeMainImage('{{ Storage::url($image) }}', this)">
                            <img src="{{ Storage::url($image) }}" 
                                 alt="{{ $product->name }} - {{ $index + 1 }}" 
                                 class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                <p class="text-gray-600">SKU: {{ $product->sku }}</p>
            </div>

            @if($product->short_description)
                <p class="text-lg text-gray-700">{{ $product->short_description }}</p>
            @endif

            <!-- Price -->
            <div class="space-y-2">
                @if($product->isOnSale())
                    <div class="flex items-center space-x-3">
                        <span class="text-3xl font-bold text-red-600">
                            R$ {{ number_format($product->sale_price, 2, ',', '.') }}
                        </span>
                        <span class="text-xl text-gray-500 line-through">
                            R$ {{ number_format($product->price, 2, ',', '.') }}
                        </span>
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm font-semibold">
                            -{{ $product->getDiscountPercentage() }}%
                        </span>
                    </div>
                @else
                    <span class="text-3xl font-bold text-gray-900">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </span>
                @endif
            </div>

            <!-- Stock Status -->
            <div class="space-y-2">
                @if($product->in_stock)
                    @if($product->manage_stock)
                        @if($product->stock_quantity <= 5)
                            <p class="text-orange-600 font-medium">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Apenas {{ $product->stock_quantity }} unidade(s) disponível(eis)
                            </p>
                        @else
                            <p class="text-green-600 font-medium">
                                <i class="fas fa-check-circle mr-1"></i>
                                Em estoque ({{ $product->stock_quantity }} disponíveis)
                            </p>
                        @endif
                    @else
                        <p class="text-green-600 font-medium">
                            <i class="fas fa-check-circle mr-1"></i>
                            Disponível
                        </p>
                    @endif
                @else
                    <p class="text-red-600 font-medium">
                        <i class="fas fa-times-circle mr-1"></i>
                        Fora de estoque
                    </p>
                @endif
            </div>

            <!-- Add to Cart -->
            @if($product->in_stock)
                <form id="add-to-cart-form" class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <label for="quantity" class="text-sm font-medium text-gray-700">Quantidade:</label>
                        <div class="flex items-center border border-gray-300 rounded-md">
                            <button type="button" onclick="changeQuantity(-1)" class="px-3 py-2 hover:bg-gray-100">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" 
                                   max="{{ $product->manage_stock ? $product->stock_quantity : 999 }}" 
                                   class="w-16 text-center border-0 focus:ring-0">
                            <button type="button" onclick="changeQuantity(1)" class="px-3 py-2 hover:bg-gray-100">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Adicionar ao Carrinho
                        </button>
                        <a href="{{ route('store.cart') }}" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-semibold">
                            <i class="fas fa-eye mr-2"></i>
                            Ver Carrinho
                        </a>
                    </div>
                </form>
            @else
                <button class="w-full bg-gray-400 text-white px-6 py-3 rounded-lg cursor-not-allowed" disabled>
                    <i class="fas fa-times mr-2"></i>
                    Produto Indisponível
                </button>
            @endif

            <!-- Product Details -->
            @if($product->description)
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-3">Descrição do Produto</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! $product->description !!}
                    </div>
                </div>
            @endif

            <!-- Product Specifications -->
            @if($product->weight || $product->length || $product->width || $product->height)
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-3">Especificações</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        @if($product->weight)
                            <div>Peso: {{ $product->weight }}kg</div>
                        @endif
                        @if($product->length && $product->width && $product->height)
                            <div>Dimensões: {{ $product->length }}x{{ $product->width }}x{{ $product->height }}cm</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Related Products -->
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