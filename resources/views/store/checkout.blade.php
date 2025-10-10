@extends('layouts.blog')

@section('title', 'Checkout - Loja')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Finalizar Compra</h1>
    
    <form action="{{ route('store.checkout.process') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @csrf
        
        <!-- Customer Information -->
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Informações de Cobrança</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                        <input type="text" id="billing_name" name="billing_name" required 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" id="billing_email" name="billing_email" required 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="billing_phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone *</label>
                        <input type="tel" id="billing_phone" name="billing_phone" required 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="billing_zip" class="block text-sm font-medium text-gray-700 mb-1">CEP *</label>
                        <input type="text" id="billing_zip" name="billing_zip" required 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">Endereço *</label>
                        <input type="text" id="billing_address" name="billing_address" required 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-1">Cidade *</label>
                        <input type="text" id="billing_city" name="billing_city" required 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="billing_state" class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <input type="text" id="billing_state" name="billing_state" required maxlength="2" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Método de Pagamento</h2>
                
                <div class="space-y-3">
                    @foreach($paymentMethods as $method)
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="payment_method_id" value="{{ $method->id }}" required 
                                   class="mr-3 text-blue-600 focus:ring-blue-500">
                            <div class="flex-1">
                                <div class="font-medium">{{ $method->name }}</div>
                                @if($method->description)
                                    <div class="text-sm text-gray-600">{{ $method->description }}</div>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <!-- Notes -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Observações</h2>
                <textarea name="notes" rows="3" placeholder="Observações sobre o pedido (opcional)" 
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 sticky top-4">
                <h2 class="text-lg font-semibold mb-4">Resumo do Pedido</h2>
                
                <!-- Cart Items -->
                <div class="space-y-3 mb-6">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between text-sm">
                            <span>{{ $item['product_name'] }} × {{ $item['quantity'] }}</span>
                            <span>R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                
                <hr class="mb-4">
                
                <!-- Totals -->
                <div class="space-y-2 mb-6">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>R$ {{ number_format($cartTotals['subtotal'], 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Frete:</span>
                        <span>R$ {{ number_format($cartTotals['shipping'], 2, ',', '.') }}</span>
                    </div>
                    @if($cartTotals['discount'] > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Desconto:</span>
                            <span>-R$ {{ number_format($cartTotals['discount'], 2, ',', '.') }}</span>
                        </div>
                    @endif
                    <hr>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span>R$ {{ number_format($cartTotals['total'], 2, ',', '.') }}</span>
                    </div>
                </div>
                
                <!-- Terms -->
                <div class="mb-6">
                    <label class="flex items-start">
                        <input type="checkbox" name="terms_accepted" required class="mt-1 mr-2 text-blue-600">
                        <span class="text-sm text-gray-700">
                            Concordo com os <a href="#" class="text-blue-600 hover:underline">termos e condições</a> e 
                            <a href="#" class="text-blue-600 hover:underline">política de privacidade</a>
                        </span>
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    <i class="fas fa-lock mr-2"></i>
                    Finalizar Pedido
                </button>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('store.cart') }}" class="text-blue-600 hover:underline text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Voltar ao Carrinho
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection