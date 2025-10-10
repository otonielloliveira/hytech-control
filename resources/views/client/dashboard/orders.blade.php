@extends('layouts.client-dashboard')

@section('title', 'Meus Pedidos')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Meus Pedidos</h1>
        <p class="text-gray-600">Acompanhe o histórico e status dos seus pedidos</p>
    </div>

    @if($orders->count() > 0)
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Pedido #{{ $order->order_number }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Realizado em {{ $order->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="mt-2 md:mt-0 flex flex-col md:items-end">
                                <div class="flex items-center space-x-2 mb-1">
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $order->getStatusLabel() }}
                                            </span>
                                            @break
                                        @case('processing')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-cog mr-1"></i>
                                                {{ $order->getStatusLabel() }}
                                            </span>
                                            @break
                                        @case('shipped')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <i class="fas fa-truck mr-1"></i>
                                                {{ $order->getStatusLabel() }}
                                            </span>
                                            @break
                                        @case('delivered')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                {{ $order->getStatusLabel() }}
                                            </span>
                                            @break
                                        @case('cancelled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                {{ $order->getStatusLabel() }}
                                            </span>
                                            @break
                                    @endswitch
                                </div>
                                <p class="text-lg font-bold text-gray-900">
                                    R$ {{ number_format($order->total, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Itens do Pedido:</h4>
                                <div class="space-y-2">
                                    @foreach($order->items->take(3) as $item)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs mr-2">
                                                {{ $item->quantity }}x
                                            </span>
                                            {{ $item->product_name }}
                                        </div>
                                    @endforeach
                                    @if($order->items->count() > 3)
                                        <p class="text-sm text-gray-500">
                                            + {{ $order->items->count() - 3 }} item(s) adicional(ais)
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Informações de Pagamento:</h4>
                                <div class="text-sm text-gray-600">
                                    <p>Método: {{ $order->paymentMethod->name ?? 'N/A' }}</p>
                                    <p>Status: 
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
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @if($order->tracking_code)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-truck text-blue-600 mr-2"></i>
                                    <div>
                                        <h5 class="text-sm font-medium text-blue-800">Código de Rastreio</h5>
                                        <p class="text-sm text-blue-700 font-mono">{{ $order->tracking_code }}</p>
                                        @if($order->tracking_url)
                                            <a href="{{ $order->tracking_url }}" target="_blank" 
                                               class="text-xs text-blue-600 hover:underline">
                                                Rastrear encomenda
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="flex justify-end">
                            <a href="{{ route('client.orders.detail', $order) }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-eye mr-2"></i>
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <div class="text-gray-400 text-6xl mb-4">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhum pedido encontrado</h3>
            <p class="text-gray-600 mb-8">Você ainda não fez nenhum pedido em nossa loja.</p>
            <a href="{{ route('store.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-shopping-cart mr-2"></i>
                Fazer Primeiro Pedido
            </a>
        </div>
    @endif
</div>
@endsection