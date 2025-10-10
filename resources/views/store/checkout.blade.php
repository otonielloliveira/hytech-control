@extends('layouts.blog')

@section('title', 'Checkout - Finalizar Compra')

@push('styles')
<style>
    .checkout-container {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 10px;
        margin-top: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .form-section {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 0.6rem 1rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }

    .btn-checkout {
        background: #007bff;
        color: white;
        padding: 14px;
        font-weight: bold;
        font-size: 1.1rem;
        border-radius: 8px;
        transition: all 0.2s;
        width: 100%;
    }

    .btn-checkout:hover {
        background: #0056b3;
        transform: translateY(-2px);
    }

    .order-summary {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        position: sticky;
        top: 20px;
    }

    .summary-item {
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .badge-security {
        font-size: 0.85rem;
        padding: 6px 12px;
    }
</style>
@endpush

@section('content')
<div class="container my-4">
    <div class="checkout-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i>Finalizar Compra</h2>
            <a href="{{ route('store.cart') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Voltar ao Carrinho
            </a>
        </div>

        <form action="{{ route('store.checkout.process') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Formulário de Dados -->
                <div class="col-lg-8">
                    <!-- Informações de Cobrança -->
                    <div class="form-section">
                        <h3 class="h5 mb-4"><i class="fas fa-user me-2 text-primary"></i>Informações de Cobrança</h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="billing_name" class="form-label">Nome Completo *</label>
                                <input type="text" id="billing_name" name="billing_name" required 
                                       class="form-control" placeholder="Digite seu nome completo"
                                       value="{{ old('billing_name') }}">
                                @error('billing_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="billing_email" class="form-label">Email *</label>
                                <input type="email" id="billing_email" name="billing_email" required 
                                       class="form-control" placeholder="seu@email.com"
                                       value="{{ old('billing_email') }}">
                                @error('billing_email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="billing_phone" class="form-label">Telefone *</label>
                                <input type="tel" id="billing_phone" name="billing_phone" required 
                                       class="form-control" placeholder="(11) 99999-9999"
                                       value="{{ old('billing_phone') }}">
                                @error('billing_phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="billing_zip" class="form-label">CEP *</label>
                                <input type="text" id="billing_zip" name="billing_zip" required 
                                       class="form-control" placeholder="00000-000"
                                       value="{{ old('billing_zip') }}">
                                @error('billing_zip')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="billing_address" class="form-label">Endereço *</label>
                                <input type="text" id="billing_address" name="billing_address" required 
                                       class="form-control" placeholder="Rua, número, complemento"
                                       value="{{ old('billing_address') }}">
                                @error('billing_address')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="billing_city" class="form-label">Cidade *</label>
                                <input type="text" id="billing_city" name="billing_city" required 
                                       class="form-control" placeholder="Digite sua cidade"
                                       value="{{ old('billing_city') }}">
                                @error('billing_city')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="billing_state" class="form-label">Estado *</label>
                                <select id="billing_state" name="billing_state" required class="form-control">
                                    <option value="">Selecione</option>
                                    <option value="SP" {{ old('billing_state') == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                    <option value="RJ" {{ old('billing_state') == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                    <option value="MG" {{ old('billing_state') == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                    <option value="RS" {{ old('billing_state') == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                    <option value="PR" {{ old('billing_state') == 'PR' ? 'selected' : '' }}>Paraná</option>
                                    <option value="SC" {{ old('billing_state') == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                    <!-- Adicione outros estados conforme necessário -->
                                </select>
                                @error('billing_state')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Método de Pagamento -->
                    <div class="form-section">
                        <h3 class="h5 mb-4"><i class="fas fa-credit-card me-2 text-success"></i>Método de Pagamento</h3>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>PIX:</strong> Pagamento rápido e seguro via PIX
                        </div>
                        <input type="hidden" name="payment_method_id" value="1">
                    </div>

                    <!-- Observações -->
                    <div class="form-section">
                        <h3 class="h5 mb-4"><i class="fas fa-sticky-note me-2 text-warning"></i>Observações (Opcional)</h3>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Alguma observação especial sobre seu pedido?">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Termos de Uso -->
                    <div class="form-section">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms_accepted" name="terms_accepted" required>
                            <label class="form-check-label fw-bold" for="terms_accepted">
                                Li e aceito os <a href="#" class="text-primary">Termos de Uso</a> e 
                                <a href="#" class="text-primary">Política de Privacidade</a> *
                            </label>
                        </div>
                        @error('terms_accepted')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Resumo do Pedido -->
                <div class="col-lg-4">
                    <div class="order-summary">
                        <h3 class="h5 mb-4"><i class="fas fa-receipt me-2"></i>Resumo do Pedido</h3>

                        @php
                            $subtotal = 0;
                            $itemCount = 0;
                            foreach($cartItems as $item) {
                                $subtotal += $item['subtotal'];
                                $itemCount += $item['quantity'];
                            }
                            $shipping = 16.90;
                            $total = $subtotal + $shipping;
                        @endphp

                        <!-- Produtos -->
                        @foreach($cartItems as $item)
                        <div class="summary-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $item['product_name'] }}</strong><br>
                                    <small class="text-muted">Qtd: {{ $item['quantity'] }}</small>
                                </div>
                                <span>R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</span>
                            </div>
                        </div>
                        @endforeach

                        <!-- Totais -->
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal ({{ $itemCount }}):</span>
                                <strong>R$ {{ number_format($subtotal, 2, ',', '.') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Frete:</span>
                                <strong>R$ {{ number_format($shipping, 2, ',', '.') }}</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fs-5 fw-bold text-success">
                                <span>Total:</span>
                                <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Botão de Finalizar -->
                        <button type="submit" class="btn-checkout mt-4">
                            <i class="fas fa-check-circle me-2"></i>Finalizar Pedido
                        </button>

                        <!-- Segurança -->
                        <div class="text-center mt-4">
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <span class="badge bg-success badge-security">🔒 SSL</span>
                                <span class="badge bg-primary badge-security">💳 PIX</span>
                                <span class="badge bg-warning text-dark badge-security">📱 Seguro</span>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Seus dados estão protegidos
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Máscara para telefone
    document.getElementById('billing_phone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 10) {
            value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else {
            value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
        }
        e.target.value = value;
    });

    // Máscara para CEP
    document.getElementById('billing_zip').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{5})(\d{3})/, '$1-$2');
        e.target.value = value;
    });

    // Validação do formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        const termsAccepted = document.getElementById('terms_accepted').checked;
        
        if (!termsAccepted) {
            e.preventDefault();
            alert('Você deve aceitar os termos de uso para continuar.');
            return;
        }
    });
</script>
@endpush