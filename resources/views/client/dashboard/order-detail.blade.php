@extends('layouts.client-dashboard')

@section('title', 'Detalhes do Pedido #' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <nav class="text-sm text-gray-600 mb-4">
            <a href="{{ route('client.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
            <span class="mx-2">›</span>
            <a href="{{ route('client.orders') }}" class="hover:text-blue-600">Meus Pedidos</a>
            <span class="mx-2">›</span>
            <span class="text-gray-900">Pedido #{{ $order->order_number }}</span>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Pedido #{{ $order->order_number }}</h1>
                <p class="text-gray-600">Realizado em {{ $order->created_at->format('d/m/Y \à\s H:i') }}</p>
            </div>
            <div class="mt-4 md:mt-0">
                @switch($order->status)
                    @case('pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-2"></i>
                            {{ $order->getStatusLabel() }}
                        </span>
                        @break
                    @case('processing')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-cog mr-2"></i>
                            {{ $order->getStatusLabel() }}
                        </span>
                        @break
                    @case('shipped')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-truck mr-2"></i>
                            {{ $order->getStatusLabel() }}
                        </span>
                        @break
                    @case('delivered')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ $order->getStatusLabel() }}
                        </span>
                        @break
                    @case('cancelled')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-2"></i>
                            {{ $order->getStatusLabel() }}
                        </span>
                        @break
                @endswitch
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Detalhes do Pedido -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Itens do Pedido -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Itens do Pedido</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <div class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400 text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-medium text-gray-900 truncate">
                                        {{ $item->product_name }}
                                    </h4>
                                    <p class="text-sm text-gray-600">SKU: {{ $item->product_sku }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Quantidade: {{ $item->quantity }}</p>
                                    <p class="text-sm text-gray-600">
                                        Preço unitário: R$ {{ number_format($item->product_price, 2, ',', '.') }}
                                    </p>
                                    <p class="text-lg font-bold text-gray-900">
                                        R$ {{ number_format($item->total, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Endereços -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Endereço de Cobrança -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Endereço de Cobrança</h3>
                    </div>
                    <div class="px-6 py-4">
                        @if($order->billing_address)
                            @php $billing = $order->billing_address; @endphp
                            <address class="text-sm text-gray-600 not-italic">
                                <strong>{{ $billing['name'] ?? 'N/A' }}</strong><br>
                                {{ $billing['address'] ?? 'N/A' }}<br>
                                {{ $billing['city'] ?? 'N/A' }}, {{ $billing['state'] ?? 'N/A' }} {{ $billing['zip'] ?? '' }}<br>
                                @if(isset($billing['phone']))
                                    Tel: {{ $billing['phone'] }}<br>
                                @endif
                                @if(isset($billing['email']))
                                    {{ $billing['email'] }}
                                @endif
                            </address>
                        @else
                            <p class="text-sm text-gray-500">Endereço não disponível</p>
                        @endif
                    </div>
                </div>

                <!-- Endereço de Entrega -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Endereço de Entrega</h3>
                    </div>
                    <div class="px-6 py-4">
                        @if($order->shipping_address)
                            @php $shipping = $order->shipping_address; @endphp
                            <address class="text-sm text-gray-600 not-italic">
                                <strong>{{ $shipping['name'] ?? 'N/A' }}</strong><br>
                                {{ $shipping['address'] ?? 'N/A' }}<br>
                                {{ $shipping['city'] ?? 'N/A' }}, {{ $shipping['state'] ?? 'N/A' }} {{ $shipping['zip'] ?? '' }}
                            </address>
                        @else
                            <p class="text-sm text-gray-500">Mesmo endereço de cobrança</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Rastreamento -->
            @if($order->tracking_code)
                <div class="bg-white rounded-lg shadow-md border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Informações de Rastreamento</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-truck text-blue-600 text-2xl mr-4"></i>
                                <div>
                                    <h4 class="text-lg font-medium text-blue-800">Código de Rastreio</h4>
                                    <p class="text-xl font-mono font-bold text-blue-900">{{ $order->tracking_code }}</p>
                                    @if($order->tracking_url)
                                        <a href="{{ $order->tracking_url }}" target="_blank" 
                                           class="inline-flex items-center mt-2 text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-external-link-alt mr-1"></i>
                                            Rastrear no site dos Correios
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Observações -->
            @if($order->notes)
                <div class="bg-white rounded-lg shadow-md border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Observações</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-gray-700">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Resumo -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 sticky top-4">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Resumo do Pedido</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Frete:</span>
                            <span class="font-medium">R$ {{ number_format($order->shipping_total, 2, ',', '.') }}</span>
                        </div>
                        
                        @if($order->discount_total > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Desconto:</span>
                                <span class="font-medium">-R$ {{ number_format($order->discount_total, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        
                        <hr class="border-gray-300">
                        
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-900">Total:</span>
                            <span class="text-gray-900">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Informações de Pagamento</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Método:</span>
                            <span class="font-medium">{{ $order->paymentMethod->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium">
                                @switch($order->payment_status)
                                    @case('pending')
                                        <span class="text-yellow-600">Pendente</span>
                                        @break
                                    @case('processing')
                                        <span class="text-blue-600">Processando</span>
                                        @break
                                    @case('completed')
                                        <span class="text-green-600">Aprovado</span>
                                        @break
                                    @case('failed')
                                        <span class="text-red-600">Falhou</span>
                                        @break
                                    @default
                                        <span class="text-gray-600">{{ ucfirst($order->payment_status) }}</span>
                                @endswitch
                            </span>
                        </div>
                        @if($order->payment_transaction_id)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Transaction ID:</span>
                                <span class="font-mono text-xs">{{ Str::limit($order->payment_transaction_id, 15) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200">
                    <a href="{{ route('client.orders') }}" 
                       class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors text-center block">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Voltar aos Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection