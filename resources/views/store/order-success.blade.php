@extends('layouts.blog')

@section('title', 'Pedido Realizado com Sucesso - Loja')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto text-center">
        <div class="text-green-500 text-6xl mb-4">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Pedido Realizado com Sucesso!</h1>
        
        <p class="text-lg text-gray-600 mb-8">
            Seu pedido <strong>#{{ $order->order_number }}</strong> foi recebido e está sendo processado.
        </p>
        
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-8 text-left">
            <h2 class="text-lg font-semibold mb-4">Detalhes do Pedido</h2>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <span class="text-sm text-gray-600">Número do Pedido:</span>
                    <div class="font-semibold">{{ $order->order_number }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Data:</span>
                    <div class="font-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Status:</span>
                    <div class="font-semibold text-blue-600">{{ $order->getStatusLabel() }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Total:</span>
                    <div class="font-semibold text-lg">R$ {{ number_format($order->total, 2, ',', '.') }}</div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <h3 class="font-semibold mb-3">Itens do Pedido:</h3>
            <div class="space-y-2">
                @foreach($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                        <span>R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        
        @if(session('payment_result'))
            @php $paymentResult = session('payment_result'); @endphp
            
            @if($paymentResult['success'])
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                    <h3 class="font-semibold text-blue-900 mb-2">Informações de Pagamento</h3>
                    
                    @if(isset($paymentResult['qr_code']))
                        <p class="text-blue-800 mb-3">Use o QR Code abaixo para pagar via PIX:</p>
                        <div class="bg-white p-4 rounded-lg inline-block">
                            <img src="{{ $paymentResult['qr_code']['qr_code_image'] ?? '#' }}" alt="QR Code PIX" class="w-48 h-48 mx-auto">
                        </div>
                        <p class="text-sm text-blue-600 mt-2">Ou copie e cole o código PIX</p>
                    @endif
                    
                    @if(isset($paymentResult['payment_url']))
                        <div class="mt-4">
                            <a href="{{ $paymentResult['payment_url'] }}" target="_blank" 
                               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                Acessar Pagamento
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        @endif
        
        <div class="space-y-4">
            <a href="{{ route('store.index') }}" 
               class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold inline-block">
                <i class="fas fa-shopping-bag mr-2"></i>
                Continuar Comprando
            </a>
            
            <div>
                <a href="{{ route('blog.index') }}" class="text-blue-600 hover:underline">
                    <i class="fas fa-home mr-1"></i>
                    Voltar ao Início
                </a>
            </div>
        </div>
    </div>
</div>
@endsection