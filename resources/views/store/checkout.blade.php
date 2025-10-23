@extends('layouts.blog')

@section('title', 'Checkout - Finalizar Compra')

@section('styles')
    <style>
        body {
            background-color: #ebebeb;
        }

        /* Header estilo ML */
        .checkout-header {
            background: #fff;
            border-bottom: 1px solid #e5e5e5;
            padding: 1rem 0;
            margin-bottom: 1.5rem;
        }

        .back-link {
            color: #3483fa;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* Cards limpos */
        .card {
            border: none;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,.1);
            margin-bottom: 1rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.25rem;
        }

        /* Form controls */
        .form-label {
            font-size: 14px;
            color: #333;
            font-weight: 400;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 1px solid #e6e6e6;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 16px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3483fa;
            box-shadow: none;
        }

        /* List group para endereços e pagamentos */
        .list-group-item {
            border: 1px solid #e6e6e6;
            border-radius: 6px !important;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .list-group-item:hover {
            border-color: #3483fa;
            background-color: #f7f7f7;
        }

        .list-group-item.active {
            background-color: #f0f7ff;
            border-color: #3483fa;
            border-width: 2px;
            color: inherit;
        }

        .list-group-item input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* Resumo do pedido */
        .order-summary {
            position: sticky;
            top: 1rem;
        }

        .summary-product {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-product:last-of-type {
            border-bottom: none;
        }

        .summary-totals {
            border-top: 1px solid #e6e6e6;
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .summary-total {
            border-top: 1px solid #e6e6e6;
            padding-top: 1rem;
            margin-top: 1rem;
            font-size: 24px;
            font-weight: 400;
        }

        /* Botão principal */
        .btn-primary {
            background-color: #3483fa;
            border-color: #3483fa;
            border-radius: 6px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #2968c8;
            border-color: #2968c8;
        }

        /* Termos */
        .terms-box {
            background: #fff9e6;
            border: 1px solid #ffe6a1;
            border-radius: 6px;
            padding: 1rem;
        }

        .terms-box .form-check-input {
            width: 16px;
            height: 16px;
            margin-top: 2px;
        }

        .terms-box a {
            color: #3483fa;
        }

        /* Security badges */
        .security-section {
            border-top: 1px solid #e6e6e6;
            padding-top: 1rem;
            margin-top: 1rem;
            text-align: center;
        }

        .security-badge {
            background: #f0f0f0;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            color: #666;
            margin: 0 4px;
        }

        .security-text {
            font-size: 12px;
            color: #00a650;
            margin-top: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .order-summary {
                position: static;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Header -->
    <div class="checkout-header">
        <div class="container">
            <a href="{{ route('store.cart') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Voltar ao carrinho
            </a>
        </div>
    </div>

    <div class="container my-4">
        <h1 class="h3 mb-4">Finalizar compra</h1>

            <form id="checkoutForm" action="{{ route('store.checkout.process') }}" method="POST">
                @csrf

                <!-- Hidden billing address fields to be filled from selected or modal address -->
                <input type="hidden" id="billing_address" name="billing_address" value="{{ old('billing_address','') }}">
                <input type="hidden" id="billing_city" name="billing_city" value="{{ old('billing_city','') }}">
                <input type="hidden" id="billing_state" name="billing_state" value="{{ old('billing_state','') }}">
                <input type="hidden" id="billing_zip" name="billing_zip" value="{{ old('billing_zip','') }}">

                <div class="row">
                    <!-- Formulário de Dados -->
                    <div class="col-lg-8">
                        <!-- Informações de Cobrança -->
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Informações de cobrança</h3>

                            <div class="row">
                                @if (isset($client) && $client)
                                    <div class="col-12 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label
                                                class="form-label mb-0">{{ $addresses->count() > 0 ? 'Endereços Salvos' : 'Nenhum endereço cadastrado' }}</label>
                                            {{-- <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                                <i class="fas fa-plus me-1"></i>Adicionar Endereço
                                            </button> --}}
                                        </div>
                                        @if ($addresses->count() > 0)
                                            <div class="list-group" id="address-list">
                                                @foreach ($addresses as $addr)
                                                    <label class="list-group-item address-select" style="cursor: pointer;"
                                                        data-address-id="{{ $addr->id }}"
                                                        data-name="{{ $addr->name ?? $client->name }}"
                                                        data-street="{{ $addr->street }}" data-number="{{ $addr->number }}"
                                                        data-complement="{{ $addr->complement }}"
                                                        data-neighborhood="{{ $addr->neighborhood }}"
                                                        data-city="{{ $addr->city }}" data-state="{{ $addr->state }}"
                                                        data-postal-code="{{ $addr->postal_code }}">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <input type="radio" name="use_address"
                                                                    value="{{ $addr->id }}"
                                                                    {{ $addr->is_default ? 'checked' : '' }}
                                                                    class="me-2">
                                                                <strong>{{ $addr->street }}, {{ $addr->number }}</strong>
                                                                @if ($addr->complement)
                                                                    - {{ $addr->complement }}
                                                                @endif
                                                                <br>
                                                                <small class="text-muted">{{ $addr->neighborhood }} -
                                                                    {{ $addr->city }}/{{ $addr->state }} | CEP:
                                                                    {{ $addr->postal_code }}</small>
                                                            </div>
                                                            @if ($addr->is_default)
                                                                <span class="badge bg-primary">Padrão</span>
                                                            @endif
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label for="billing_name" class="form-label">Nome Completo *</label>
                                    <input type="text" id="billing_name" name="billing_name" required
                                        class="form-control" placeholder="Digite seu nome completo"
                                        value="{{ old('billing_name', $client->name ?? '') }}">
                                    @error('billing_name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="billing_email" class="form-label">Email *</label>
                                    <input type="email" id="billing_email" name="billing_email" required
                                        class="form-control" placeholder="seu@email.com"
                                        value="{{ old('billing_email', $client->email ?? '') }}">
                                    @error('billing_email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="billing_phone" class="form-label">Telefone *</label>
                                    <input type="tel" id="billing_phone" name="billing_phone" required
                                        class="form-control" placeholder="(11) 99999-9999"
                                        value="{{ old('billing_phone', $client->phone ?? '') }}">
                                    @error('billing_phone')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>


                            </div>
                            </div>
                        </div>

                        <!-- Método de Pagamento -->
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Forma de pagamento</h3>
                                <div class="list-group" id="payment-method-list">
                                    @foreach ($paymentMethods as $method)
                                        <div class="list-group-item d-flex justify-content-between align-items-center {{ $loop->first ? 'active' : '' }}" 
                                             data-gateway="{{ $method->gateway }}"
                                             onclick="selectPayment(this, '{{ $method->gateway }}')">
                                            <div>
                                                <strong>{{ $method->name }}</strong>
                                                <div class="text-muted small">{{ $method->description }}</div>
                                            </div>
                                            <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                                                data-gateway="{{ $method->gateway }}"
                                                {{ $loop->first ? 'checked' : '' }}>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Campos específicos para Cartão de Crédito -->
                                <div id="card-payment-fields" class="mt-4" style="display: none;">
                                    <h5 class="mb-3" style="font-size: 16px; font-weight: 600;">Dados do cartão</h5>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="card_number" class="form-label">Número do cartão *</label>
                                            <input type="text" id="card_number" class="form-control" 
                                                   placeholder="0000 0000 0000 0000" maxlength="19">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="card_holder" class="form-label">Nome no cartão *</label>
                                            <input type="text" id="card_holder" class="form-control" 
                                                   placeholder="Nome como está no cartão">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="card_expiry" class="form-label">Validade *</label>
                                            <input type="text" id="card_expiry" class="form-control" 
                                                   placeholder="MM/AA" maxlength="5">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="card_cvv" class="form-label">CVV *</label>
                                            <input type="text" id="card_cvv" class="form-control" 
                                                   placeholder="000" maxlength="4">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="card_installments" class="form-label">Parcelas *</label>
                                            <select id="card_installments" class="form-select">
                                                <option value="1">1x sem juros</option>
                                                <option value="2">2x sem juros</option>
                                                <option value="3">3x sem juros</option>
                                                <option value="4">4x sem juros</option>
                                                <option value="5">5x sem juros</option>
                                                <option value="6">6x sem juros</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campos específicos para Boleto -->
                                <div id="boleto-payment-fields" class="mt-4" style="display: none;">
                                    <div class="alert alert-info" style="border-radius: 6px; font-size: 14px;">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Pagamento via Boleto Bancário</strong>
                                        <p class="mb-0 mt-2">Após finalizar a compra, você receberá o boleto por e-mail. O prazo de compensação é de até 2 dias úteis.</p>
                                    </div>
                                </div>

                                <!-- Campos específicos para PIX -->
                                <div id="pix-payment-fields" class="mt-4" style="display: none;">
                                    <div class="alert alert-success" style="border-radius: 6px; font-size: 14px;">
                                        <i class="fas fa-qrcode me-2"></i>
                                        <strong>Pagamento via PIX</strong>
                                        <p class="mb-0 mt-2">Após finalizar a compra, você receberá um QR Code para pagamento instantâneo. Aprovação imediata!</p>
                                    </div>
                                </div>

                                <input type="hidden" name="payment_data" id="payment_data" value="">
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Observações (opcional)</h3>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Alguma observação sobre seu pedido?">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- Termos de Uso -->
                        <div class="terms-box">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms_accepted"
                                    name="terms_accepted" required>
                                <label class="form-check-label" for="terms_accepted" style="font-size: 13px;">
                                    Li e aceito os <a href="#">Termos de Uso</a> e a <a href="#">Política de Privacidade</a> *
                                </label>
                            </div>
                            @error('terms_accepted')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Resumo do Pedido -->
                    <div class="col-lg-4">
                        <div class="card order-summary">
                            <div class="card-body">
                                <h3 class="card-title">Resumo da compra</h3>

                            @php
                                $subtotal = 0;
                                $itemCount = 0;
                                foreach ($cartItems as $item) {
                                    $subtotal += $item['subtotal'];
                                    $itemCount += $item['quantity'];
                                }
                                $shipping = 16.9;
                                $total = $subtotal + $shipping;
                            @endphp

                                <!-- Produtos -->
                                @foreach ($cartItems as $item)
                                    <div class="summary-product">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div style="font-size: 14px; color: #333;">{{ $item['product_name'] }}</div>
                                                <small class="text-muted">Quantidade: {{ $item['quantity'] }}</small>
                                            </div>
                                            <span style="font-size: 14px; white-space: nowrap;">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Totais -->
                                <div class="summary-totals">
                                    <div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
                                        <span>Produtos ({{ $itemCount }})</span>
                                        <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
                                        <span>Frete</span>
                                        <span>R$ {{ number_format($shipping, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between summary-total">
                                        <span>Total</span>
                                        <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- Botão de Finalizar -->
                                <button type="submit" class="btn btn-primary w-100 mt-3">
                                    Finalizar compra
                                </button>

                                @if(session('payment_result'))
                                    @php $paymentResult = session('payment_result'); @endphp
                                    <div class="mt-3">
                                        @include('components.qr-code', ['paymentResult' => $paymentResult])
                                    </div>
                                @endif

                                <!-- Segurança -->
                                <div class="security-section">
                                    <div>
                                        <span class="security-badge">🔒 Compra segura</span>
                                        <span class="security-badge">💳 PIX</span>
                                    </div>
                                    <div class="security-text">
                                        <i class="fas fa-shield-alt"></i> Seus dados estão protegidos
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
    </div>

    <!-- Modal para Adicionar Novo Endereço -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAddressModalLabel">
                        <i class="fas fa-map-marker-alt me-2"></i>Adicionar Novo Endereço
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="newAddressForm">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="new_name" class="form-label">Nome do Endereço *</label>
                                <input type="text" id="new_name" name="name" required class="form-control"
                                    placeholder="Ex: Casa, Trabalho, Avó, etc">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="new_postal_code" class="form-label">CEP *</label>
                                <input type="text" id="new_postal_code" name="postal_code" required
                                    class="form-control" placeholder="00000-000" maxlength="9">
                                <small class="text-muted">Preencha o CEP para buscar o endereço</small>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="new_street" class="form-label">Rua *</label>
                                <input type="text" id="new_street" name="street" required class="form-control"
                                    placeholder="Nome da rua">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="new_number" class="form-label">Número *</label>
                                <input type="text" id="new_number" name="number" required class="form-control"
                                    placeholder="123">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="new_complement" class="form-label">Complemento</label>
                                <input type="text" id="new_complement" name="complement" class="form-control"
                                    placeholder="Apto, Bloco, etc">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="new_neighborhood" class="form-label">Bairro *</label>
                                <input type="text" id="new_neighborhood" name="neighborhood" required
                                    class="form-control" placeholder="Nome do bairro">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="new_city" class="form-label">Cidade *</label>
                                <input type="text" id="new_city" name="city" required class="form-control"
                                    placeholder="Nome da cidade">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="new_state" class="form-label">Estado *</label>
                                <select id="new_state" name="state" required class="form-control">
                                    <option value="">Selecione</option>
                                    <option value="AC">Acre</option>
                                    <option value="AL">Alagoas</option>
                                    <option value="AP">Amapá</option>
                                    <option value="AM">Amazonas</option>
                                    <option value="BA">Bahia</option>
                                    <option value="CE">Ceará</option>
                                    <option value="DF">Distrito Federal</option>
                                    <option value="ES">Espírito Santo</option>
                                    <option value="GO">Goiás</option>
                                    <option value="MA">Maranhão</option>
                                    <option value="MT">Mato Grosso</option>
                                    <option value="MS">Mato Grosso do Sul</option>
                                    <option value="MG">Minas Gerais</option>
                                    <option value="PA">Pará</option>
                                    <option value="PB">Paraíba</option>
                                    <option value="PR">Paraná</option>
                                    <option value="PE">Pernambuco</option>
                                    <option value="PI">Piauí</option>
                                    <option value="RJ">Rio de Janeiro</option>
                                    <option value="RN">Rio Grande do Norte</option>
                                    <option value="RS">Rio Grande do Sul</option>
                                    <option value="RO">Rondônia</option>
                                    <option value="RR">Roraima</option>
                                    <option value="SC">Santa Catarina</option>
                                    <option value="SP">São Paulo</option>
                                    <option value="SE">Sergipe</option>
                                    <option value="TO">Tocantins</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="new_is_default"
                                        name="is_default" value="1">
                                    <label class="form-check-label" for="new_is_default">
                                        Definir como endereço padrão
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveAddressBtn">
                        <i class="fas fa-save me-1"></i>Salvar Endereço
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Expose server-side messages to JavaScript
        window.SERVER_ERRORS = [];
        window.SERVER_ERROR = null;
        @if ($errors->any())
            window.SERVER_ERRORS = {!! json_encode($errors->all()) !!};
        @endif
        @if (session('error'))
            window.SERVER_ERROR = {!! json_encode(session('error')) !!};
        @endif
    </script>
    
    <!-- Main checkout script - handles all interactions -->
    <script src="{{ asset('js/checkout.js') }}"></script>
@endsection
