@extends('layouts.blog')

@section('title', 'Carrinho - Loja')

@push('styles')
<style>
    .cart-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .cart-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .cart-item-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    
    .cart-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .cart-summary {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        position: sticky;
        top: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .btn-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: bold;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    
    .btn-gradient-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        color: white;
    }
    
    .btn-gradient-secondary {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        color: white;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: bold;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    
    .btn-gradient-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        color: white;
    }
    
    .quantity-control {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 50px;
        padding: 8px 15px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .quantity-btn {
        background: #667eea;
        color: white;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .quantity-btn:hover {
        background: #5a67d8;
        transform: scale(1.1);
    }
    
    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
        border: 3px solid #e9ecef;
    }
    
    .price-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 15px;
        border-radius: 25px;
        font-weight: bold;
        font-size: 1.1em;
    }
    
    .cart-badge {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: bold;
        display: inline-block;
    }
    
    .delivery-info {
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .empty-cart {
        text-align: center;
        padding: 5rem 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .empty-cart-icon {
        font-size: 6rem;
        color: #cbd5e0;
        margin-bottom: 2rem;
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-30px); }
        60% { transform: translateY(-15px); }
    }
    
    .security-badges {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    
    .security-badge {
        background: rgba(255,255,255,0.2);
        padding: 10px 15px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9em;
    }
</style>
@endpush

@section('content')
<div class="cart-container">
    <div class="container">
        <!-- Header -->
        <div class="cart-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="mb-2">🛒 Seu Carrinho</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0" style="background: transparent;">
                            <li class="breadcrumb-item">
                                <a href="{{ route('blog.index') }}" class="text-white-50">
                                    <i class="fas fa-home"></i> Início
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('store.index') }}" class="text-white-50">
                                    <i class="fas fa-store"></i> Loja
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-white">
                                <i class="fas fa-shopping-cart"></i> Carrinho
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="cart-badge">
                    <i class="fas fa-shopping-bag me-2"></i>
                    {{ count($cartItems) }} {{ count($cartItems) === 1 ? 'item' : 'itens' }}
                </div>
            </div>
        </div>

    <div class="container mx-auto px-4 py-8">
        @if(count($cartItems) > 0)
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="cart-item-card">
                        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                            <h3 class="mb-0">
                                <i class="fas fa-list-ul me-2"></i>
                                📦 Produtos Selecionados
                                <span class="badge bg-light text-dark ms-auto">{{ count($cartItems) }} produtos</span>
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <!-- Desktop Table -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-borderless mb-0">
                                    <thead style="background: #f8f9fa;">
                                        <tr>
                                            <th class="py-3 ps-4">Produto</th>
                                            <th class="py-3 text-center">Quantidade</th>
                                            <th class="py-3 text-center">Preço Unit.</th>
                                            <th class="py-3 text-center">Total</th>
                                            <th class="py-3 text-center">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cartItems as $itemId => $item)
                                        <tr data-item-id="{{ $itemId }}" style="border-bottom: 1px solid #f1f3f4;">
                                            <td class="py-4 ps-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $item['product_image'] }}" 
                                                         alt="{{ $item['product_name'] }}" 
                                                         class="product-image me-3">
                                                    <div>
                                                        <h5 class="mb-1 fw-bold">{{ $item['product_name'] }}</h5>
                                                        <small class="text-muted">{{ $item['product_sku'] ?? 'Necessidade Pública' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 text-center">
                                                <div class="quantity-control mx-auto" style="width: fit-content;">
                                                    <button onclick="updateQuantity('{{ $itemId }}', -1)" class="quantity-btn">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <span class="fw-bold fs-5">{{ $item['quantity'] }}</span>
                                                    <button onclick="updateQuantity('{{ $itemId }}', 1)" class="quantity-btn">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="py-4 text-center">
                                                <span class="fw-bold">R$ {{ number_format($item['product_price'], 2, ',', '.') }}</span>
                                            </td>
                                            <td class="py-4 text-center">
                                                <span class="price-badge">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</span>
                                            </td>
                                            <td class="py-4 text-center">
                                                <button onclick="removeItem('{{ $itemId }}')" 
                                                        class="btn btn-outline-danger btn-sm rounded-pill">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Mobile Cards -->
                            <div class="d-md-none">
                                @foreach($cartItems as $itemId => $item)
                                <div class="p-4 border-bottom" data-item-id="{{ $itemId }}">
                                    <div class="d-flex">
                                        <img src="{{ $item['product_image'] }}" 
                                             alt="{{ $item['product_name'] }}" 
                                             class="product-image me-3">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $item['product_name'] }}</h6>
                                            <small class="text-muted d-block mb-3">{{ $item['product_sku'] ?? 'Necessidade Pública' }}</small>
                                            
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="quantity-control">
                                                    <button onclick="updateQuantity('{{ $itemId }}', -1)" class="quantity-btn">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <span class="fw-bold">{{ $item['quantity'] }}</span>
                                                    <button onclick="updateQuantity('{{ $itemId }}', 1)" class="quantity-btn">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="text-end">
                                                    <div class="price-badge">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</div>
                                                    <small class="text-muted">R$ {{ number_format($item['product_price'], 2, ',', '.') }} cada</small>
                                                </div>
                                            </div>
                                            
                                            <button onclick="removeItem('{{ $itemId }}')" 
                                                    class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash me-1"></i>Remover
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                        <!-- Mobile View -->
                        <div class="block lg:hidden divide-y divide-gray-50">
                            @foreach($cartItems as $itemId => $item)
                                <div class="p-6 cart-item cart-item-animation hover:bg-gray-50 transition-colors" data-item-id="{{ $itemId }}">
                                    <div class="flex space-x-4">
                                        <div class="flex-shrink-0 relative">
                                            <img src="{{ $item['product_image'] }}" 
                                                 alt="{{ $item['product_name'] }}" 
                                                 class="w-24 h-24 object-cover rounded-xl border-2 border-gray-200 shadow-lg transform transition-transform hover:scale-105">
                                            <div class="absolute -top-2 -right-2 bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">
                                                {{ $item['quantity'] }}
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-xl text-gray-900 mb-1 line-clamp-2">{{ $item['product_name'] }}</h3>
                                            <p class="text-sm text-gray-500 mb-3 bg-gray-100 inline-block px-2 py-1 rounded-full">
                                                <i class="fas fa-tag mr-1"></i>{{ $item['product_sku'] ?? 'Necessidade Pública' }}
                                            </p>
                                            
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center space-x-3 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-3 shadow-inner">
                                                    <button onclick="updateQuantity('{{ $itemId }}', -1)" 
                                                            class="quantity-control w-10 h-10 rounded-full bg-white shadow-md flex items-center justify-center text-red-600 hover:bg-red-50 transition-all">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <span class="font-bold text-xl w-12 text-center bg-white px-3 py-2 rounded-lg shadow">{{ $item['quantity'] }}</span>
                                                    <button onclick="updateQuantity('{{ $itemId }}', 1)" 
                                                            class="quantity-control w-10 h-10 rounded-full bg-white shadow-md flex items-center justify-center text-green-600 hover:bg-green-50 transition-all">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                                        R$ {{ number_format($item['subtotal'], 2, ',', '.') }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">R$ {{ number_format($item['product_price'], 2, ',', '.') }} cada</p>
                                                </div>
                                            </div>
                                            
                                            <button onclick="removeItem('{{ $itemId }}')" 
                                                    class="text-red-600 hover:text-red-800 font-medium text-sm transition-colors bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg">
                                                <i class="fas fa-trash mr-1"></i>Remover item
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table View -->
                        <div class="hidden lg:block">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                <div class="grid grid-cols-12 gap-4 items-center text-sm font-semibold text-gray-700 uppercase tracking-wide">
                                    <div class="col-span-5">Produto</div>
                                    <div class="col-span-2 text-center">Quantidade</div>
                                    <div class="col-span-2 text-center">Preço Unitário</div>
                                    <div class="col-span-2 text-center">Total</div>
                                    <div class="col-span-1 text-center">Ação</div>
                                </div>
                            </div>

                            <div class="divide-y divide-gray-100">
                                @foreach($cartItems as $itemId => $item)
                                    <div class="p-6 cart-item hover:bg-gray-50 transition-colors" data-item-id="{{ $itemId }}">
                                        <div class="grid grid-cols-12 gap-4 items-center">
                                            <!-- Product Info -->
                                            <div class="col-span-5">
                                                <div class="flex items-center space-x-4">
                                                    <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 border-2 border-gray-200">
                                                        <img src="{{ $item['product_image'] }}" 
                                                             alt="{{ $item['product_name'] }}" 
                                                             class="w-full h-full object-cover">
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <h3 class="text-lg font-bold text-gray-900 mb-1">
                                                            {{ $item['product_name'] }}
                                                        </h3>
                                                        <p class="text-sm text-gray-500">
                                                            {{ $item['product_sku'] ?? 'Necessidade Pública' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Quantity Controls -->
                                            <div class="col-span-2 text-center">
                                                <div class="flex items-center justify-center space-x-3 bg-gray-50 rounded-lg p-2 max-w-fit mx-auto">
                                                    <button onclick="updateQuantity('{{ $itemId }}', -1)" 
                                                            class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-600 hover:bg-red-50 hover:text-red-600 transition-colors">
                                                        −
                                                    </button>
                                                    <span class="font-bold text-lg w-8 text-center bg-white px-3 py-1 rounded">{{ $item['quantity'] }}</span>
                                                    <button onclick="updateQuantity('{{ $itemId }}', 1)" 
                                                            class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-600 hover:bg-green-50 hover:text-green-600 transition-colors">
                                                        +
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Unit Price -->
                                            <div class="col-span-2 text-center">
                                                <span class="text-lg font-semibold text-gray-900">
                                                    R$ {{ number_format($item['product_price'], 2, ',', '.') }}
                                                </span>
                                            </div>
                                            
                                            <!-- Total Price -->
                                            <div class="col-span-2 text-center">
                                                <span class="text-xl font-bold text-blue-600">
                                                    R$ {{ number_format($item['subtotal'], 2, ',', '.') }}
                                                </span>
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="col-span-1 text-center">
                                                <button onclick="removeItem('{{ $itemId }}')" 
                                                        class="w-10 h-10 rounded-full bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors">
                                                    🗑️
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="xl:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-8">
                        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                💰 Resumo do Pedido
                            </h2>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Items Summary -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-center py-2">
                                        <span class="text-gray-700 flex items-center font-medium">
                                            🛍️ Produtos ({{ array_sum(array_column($cartItems, 'quantity')) }})
                                        </span>
                                        <span class="font-bold text-gray-900">R$ {{ number_format($cartTotals['subtotal'], 2, ',', '.') }}</span>
                                    </div>
                                </div>
                                
                                <!-- Shipping -->
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-700 flex items-center font-medium">
                                        🚚 Frete
                                    </span>
                                    <span class="font-bold text-gray-900">R$ {{ number_format($cartTotals['shipping'], 2, ',', '.') }}</span>
                                </div>
                                
                                <!-- Total -->
                                <div class="border-t border-gray-200 pt-4">
                                    <div class="flex justify-between items-center py-3 bg-blue-50 rounded-lg px-4">
                                        <span class="text-xl font-bold text-gray-900">💳 Total</span>
                                        <span class="text-2xl font-bold text-blue-600">R$ {{ number_format($cartTotals['total'], 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Delivery Info -->
                            <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-start space-x-3">
                                    <span class="text-green-600 text-lg">📦</span>
                                    <div>
                                        <h4 class="font-bold text-green-800">Entrega Estimada</h4>
                                        <p class="text-sm text-green-700 mt-1">5-7 dias úteis</p>
                                        <p class="text-xs text-green-600 mt-1">Frete grátis em compras acima de R$ 150,00</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="mt-8 space-y-4">
                                <a href="{{ route('store.checkout') }}" 
                                   class="w-full cart-button bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800 text-white py-5 rounded-xl font-bold text-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 shadow-xl hover:shadow-2xl text-center block relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-20 transform -skew-x-12 translate-x-full group-hover:translate-x-0 transition-transform duration-700"></div>
                                    <span class="relative z-10">
                                        <i class="fas fa-credit-card mr-3"></i>
                                        � Finalizar Compra
                                    </span>
                                </a>
                                
                                <a href="{{ route('store.index') }}" 
                                   class="w-full cart-button bg-gradient-to-r from-orange-500 to-red-500 text-white py-4 rounded-xl font-bold text-lg hover:from-orange-600 hover:to-red-600 transition-all duration-300 shadow-lg text-center block">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    🛒 Adicionar Mais Produtos
                                </a>
                                
                                <button onclick="clearCart()" 
                                        class="w-full cart-button bg-gradient-to-r from-red-100 to-pink-100 text-red-700 border-2 border-red-300 py-3 rounded-xl font-semibold hover:from-red-200 hover:to-pink-200 transition-all duration-300">
                                    <i class="fas fa-trash mr-2"></i>
                                    🗑️ Limpar Carrinho
                                </button>
                            </div>
                            
                            <!-- Security & Trust Badges -->
                            <div class="mt-8 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-4 border border-gray-200">
                                <div class="text-center mb-3">
                                    <h5 class="font-bold text-gray-800 text-lg">🔐 Compra 100% Segura</h5>
                                </div>
                                <div class="flex items-center justify-center space-x-6 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <span class="text-green-600 mr-2 text-lg">�️</span>
                                        <span class="font-medium">SSL 256-bit</span>
                                    </div>
                                    <div class="w-px h-4 bg-gray-300"></div>
                                    <div class="flex items-center">
                                        <span class="text-blue-600 mr-2 text-lg">💳</span>
                                        <span class="font-medium">Pagamento Seguro</span>
                                    </div>
                                    <div class="w-px h-4 bg-gray-300"></div>
                                    <div class="flex items-center">
                                        <span class="text-purple-600 mr-2 text-lg">⚡</span>
                                        <span class="font-medium">Entrega Rápida</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Info -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <div class="flex items-start space-x-4">
                    <span class="text-blue-600 text-2xl">ℹ️</span>
                    <div>
                        <h4 class="text-lg font-bold text-blue-800 mb-2">Informações Importantes</h4>
                        <ul class="text-blue-700 space-y-1">
                            <li>• Atualmente o frete é fixo de acordo com a quantidade de itens do carrinho</li>
                            <li>• O prazo de entrega começa a contar a partir da aprovação do pagamento</li>
                            <li>• Todos os produtos são cuidadosamente embalados para garantir a integridade durante o transporte</li>
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-20 text-center cart-item-animation">
                <div class="max-w-lg mx-auto">
                    <div class="relative mb-8">
                        <div class="text-gray-300 text-9xl mb-4 animate-bounce">🛒</div>
                        <div class="absolute -top-4 -right-4 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-xl font-bold animate-pulse">
                            0
                        </div>
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        Ops! Seu carrinho está <span class="text-red-500">vazio</span>
                    </h2>
                    <p class="text-gray-600 mb-8 text-xl leading-relaxed">
                        Que tal explorar nossa incrível seleção de produtos? <br>
                        Temos livros, cursos e muito mais esperando por você! 🎯
                    </p>
                    
                    <div class="space-y-4">
                        <a href="{{ route('store.index') }}" 
                           class="inline-flex items-center px-10 py-5 bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800 text-white font-bold text-xl rounded-2xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 cart-button">
                            <span class="mr-3 text-2xl">🎉</span>
                            Descobrir Produtos Incríveis
                            <span class="ml-3 text-2xl">✨</span>
                        </a>
                        
                        <div class="flex items-center justify-center space-x-6 mt-8 text-gray-500">
                            <div class="flex items-center">
                                <span class="text-green-500 mr-2 text-xl">🚚</span>
                                <span class="text-sm font-medium">Frete Grátis</span>
                            </div>
                            <div class="w-px h-4 bg-gray-300"></div>
                            <div class="flex items-center">
                                <span class="text-blue-500 mr-2 text-xl">�</span>
                                <span class="text-sm font-medium">Compra Segura</span>
                            </div>
                            <div class="w-px h-4 bg-gray-300"></div>
                            <div class="flex items-center">
                                <span class="text-purple-500 mr-2 text-xl">⚡</span>
                                <span class="text-sm font-medium">Entrega Rápida</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Enhanced cart functionality with better UX
function updateQuantity(itemId, change) {
    const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
    const quantitySpan = cartItem.querySelector('span');
    const currentQuantity = parseInt(quantitySpan.textContent);
    const newQuantity = Math.max(1, currentQuantity + change);
    
    // Visual feedback
    quantitySpan.style.transform = 'scale(1.2)';
    quantitySpan.style.color = '#3B82F6';
    
    setTimeout(() => {
        quantitySpan.style.transform = 'scale(1)';
        quantitySpan.style.color = '';
    }, 200);
    
    quantitySpan.textContent = newQuantity;
    updateCart([{id: itemId, quantity: newQuantity}]);
}

function removeItem(itemId) {
    Swal.fire({
        title: '🗑️ Remover item?',
        text: 'Tem certeza que deseja remover este item do carrinho?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: '✅ Sim, remover!',
        cancelButtonText: '❌ Cancelar',
        background: '#FFFFFF',
        customClass: {
            popup: 'rounded-2xl shadow-2xl',
            title: 'text-2xl font-bold',
            content: 'text-lg'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
            
            // Smooth removal animation
            cartItem.style.transform = 'translateX(-100%)';
            cartItem.style.opacity = '0';
            
            setTimeout(() => {
                fetch('/loja/carrinho/remover', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ cart_item_id: itemId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '✅ Removido!',
                            text: 'Item removido com sucesso!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'rounded-2xl'
                            }
                        });
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        throw new Error('Erro ao remover item');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: '❌ Erro!',
                        text: 'Não foi possível remover o item. Tente novamente.',
                        icon: 'error',
                        customClass: {
                            popup: 'rounded-2xl'
                        }
                    });
                    // Restore item
                    cartItem.style.transform = 'translateX(0)';
                    cartItem.style.opacity = '1';
                });
            }, 300);
        }
    });
}

function clearCart() {
    Swal.fire({
        title: '🗑️ Limpar carrinho?',
        text: 'Esta ação irá remover TODOS os itens do seu carrinho!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: '🗑️ Sim, limpar tudo!',
        cancelButtonText: '❌ Cancelar',
        background: '#FFFFFF',
        customClass: {
            popup: 'rounded-2xl shadow-2xl',
            title: 'text-2xl font-bold',
            content: 'text-lg'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: '🔄 Limpando carrinho...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/loja/carrinho/limpar', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '✅ Carrinho limpo!',
                        text: 'Todos os itens foram removidos com sucesso!',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: '❌ Erro!',
                    text: 'Não foi possível limpar o carrinho. Tente novamente.',
                    icon: 'error'
                });
            });
        }
    });
}

function updateCart(items) {
    // Show subtle loading indicator
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    fetch('/loja/carrinho/atualizar', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ items: items })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success feedback
            button.innerHTML = '<i class="fas fa-check text-green-600"></i>';
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
                location.reload();
            }, 1000);
        } else {
            throw new Error('Update failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalText;
        button.disabled = false;
        
        Swal.fire({
            title: '❌ Erro!',
            text: 'Não foi possível atualizar o carrinho. Tente novamente.',
            icon: 'error',
            timer: 3000,
            showConfirmButton: false,
            customClass: {
                popup: 'rounded-2xl'
            }
        });
    });
}

// Enhanced page load animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate cart items on load
    const cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(30px)';
        setTimeout(() => {
            item.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 150);
    });
    
    // Add click effects to buttons
    const buttons = document.querySelectorAll('.cart-button');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Ripple effect
            const ripple = document.createElement('span');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            button.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});

// Add SweetAlert2 if not already included
if (typeof Swal === 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    document.head.appendChild(script);
}
</script>

<style>
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}
</style>
@endpush
@endsection