@extends('layouts.blog')

@section('title', 'Checkout - Finalizar Compra')

@push('styles')
    <style>
        .checkout-container {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-top: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .form-section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.6rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
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
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 20px;
        }

        /* Address list tweaks */
        #address-list {
            max-height: 240px;
            overflow-y: auto;
        }

        .address-select:hover {
            background: #f8f9fa;
        }

        .modal-lg {
            max-width: 900px;
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
                        <div class="form-section">
                            <h3 class="h5 mb-4"><i class="fas fa-user me-2 text-primary"></i>Informações de Cobrança</h3>

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

                        <!-- Método de Pagamento -->
                        <div class="form-section">
                            <h3 class="h5 mb-4"><i class="fas fa-credit-card me-2 text-success"></i>Método de Pagamento</h3>
                            <div class="mb-3">
                                <label class="form-label">Escolha a forma de pagamento</label>
                                <div class="list-group" id="payment-method-list">
                                    @foreach ($paymentMethods as $method)
                                        <label
                                            class="list-group-item d-flex justify-content-between align-items-center payment-method-item"
                                            data-gateway="{{ $method->gateway }}">
                                            <div>
                                                <strong>{{ $method->name }}</strong>
                                                <div class="text-muted small">{{ $method->description }}</div>
                                            </div>
                                            <div>
                                                <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                                                    data-gateway="{{ $method->gateway }}"
                                                    {{ $loop->first ? 'checked' : '' }}>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>



                            <input type="hidden" name="payment_data" id="payment_data" value="">
                        </div>

                        <!-- Observações -->
                        <div class="form-section">
                            <h3 class="h5 mb-4"><i class="fas fa-sticky-note me-2 text-warning"></i>Observações (Opcional)
                            </h3>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Alguma observação especial sobre seu pedido?">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Termos de Uso -->
                        <div class="form-section">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms_accepted"
                                    name="terms_accepted" required>
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
                                foreach ($cartItems as $item) {
                                    $subtotal += $item['subtotal'];
                                    $itemCount += $item['quantity'];
                                }
                                $shipping = 16.9;
                                $total = $subtotal + $shipping;
                            @endphp

                            <!-- Produtos -->
                            @foreach ($cartItems as $item)
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

                            @if(session('payment_result'))
                                @php $paymentResult = session('payment_result'); @endphp
                                <div class="mt-3">
                                    @include('components.qr-code', ['paymentResult' => $paymentResult])
                                </div>
                            @endif

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

@push('scripts')
    <script>
        // Server-side messages (validation errors or session error) will be exposed to JS
        window.SERVER_ERRORS = [];
        window.SERVER_ERROR = null;
        @if ($errors->any())
            window.SERVER_ERRORS = {!! json_encode($errors->all()) !!};
        @endif
        @if (session('error'))
            window.SERVER_ERROR = {!! json_encode(session('error')) !!};
        @endif

        document.addEventListener('DOMContentLoaded', function() {
            const checkoutForm = document.getElementById('checkoutForm');

            // Show server-side errors (if any)
            if (window.SERVER_ERROR) {
                try { showToast(window.SERVER_ERROR, 'error', 5000); } catch (e) { console.error(e); }
            }
            if (Array.isArray(window.SERVER_ERRORS) && window.SERVER_ERRORS.length) {
                window.SERVER_ERRORS.forEach(msg => {
                    try { showToast(msg, 'error', 5000); } catch (e) { console.error(e); }
                });
            }

            // Safe selectors
            const billingPhone = document.getElementById('billing_phone');
            const billingZip = document.getElementById('billing_zip');

            // Simple toast helper using Bootstrap Toasts
            function showToast(message, type = 'info', timeout = 3000) {
                // container
                let container = document.getElementById('toast-container-js');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'toast-container-js';
                    container.style.position = 'fixed';
                    container.style.top = '1rem';
                    container.style.right = '1rem';
                    container.style.zIndex = 11000;
                    document.body.appendChild(container);
                }

                const toastEl = document.createElement('div');
                toastEl.className = 'toast align-items-center text-bg-' + (type === 'error' ? 'danger' : (type === 'success' ? 'success' : (type === 'warning' ? 'warning text-dark' : 'primary'))) + ' border-0';
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', 'assertive');
                toastEl.setAttribute('aria-atomic', 'true');
                toastEl.style.minWidth = '220px';

                toastEl.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                `;

                container.appendChild(toastEl);
                const bsToast = new bootstrap.Toast(toastEl, { delay: timeout });
                bsToast.show();
                // remove after hidden
                toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
            }

            // Helper: apply phone mask
            function applyPhoneMask(el) {
                if (!el) return;
                el.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 10) {
                        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                    } else {
                        value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                    }
                    e.target.value = value;
                });
            }

            // Helper: apply CEP mask
            function applyCepMask(el) {
                if (!el) return;
                el.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 5) {
                        value = value.replace(/(\d{5})(\d{1,3})/, '$1-$2');
                    }
                    e.target.value = value;
                });
            }

            applyPhoneMask(billingPhone);
            applyCepMask(billingZip);

            // CEP lookup helper (via ViaCEP) - accepts element id prefixes
            function setupCepLookup(inputEl, onSuccess) {
                if (!inputEl) return;

                function lookup() {
                    const cep = inputEl.value.replace(/\D/g, '');
                    if (cep.length !== 8) return;
                    inputEl.classList.remove('is-invalid');
                    inputEl.classList.add('is-valid');
                    fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.erro) {
                                inputEl.classList.add('is-invalid');
                                inputEl.classList.remove('is-valid');
                                return;
                            }
                            inputEl.classList.remove('is-invalid');
                            inputEl.classList.add('is-valid');
                            onSuccess(data);
                        })
                        .catch(() => {
                            inputEl.classList.add('is-invalid');
                            inputEl.classList.remove('is-valid');
                        });
                }

                inputEl.addEventListener('keyup', function() {
                    lookup();
                });
                inputEl.addEventListener('blur', function() {
                    lookup();
                });
            }

            // billing CEP -> fill billing address fields
            setupCepLookup(billingZip, function(data) {
                const street = data.logradouro || '';
                const neighborhood = data.bairro || '';
                const city = data.localidade || '';
                const state = data.uf || '';
                if (street) document.getElementById('new_street').value = street;
                if (city) document.getElementById('new_city').value = city;
                if (state) document.getElementById('new_state').value = state;
            });

            // modal CEP
            const newPostalCode = document.getElementById('new_postal_code');
            setupCepLookup(newPostalCode, function(data) {
                document.getElementById('new_street').value = data.logradouro || '';
                document.getElementById('new_neighborhood').value = data.bairro || '';
                document.getElementById('new_city').value = data.localidade || '';
                document.getElementById('new_state').value = data.uf || '';
                document.getElementById('new_number').focus();
            });

            // If client has saved addresses, do not prefill billing address fields (we'll use selected address)
            const addressList = document.getElementById('address-list');
            if (addressList && addressList.children.length > 0) {
                // Clear any prefilled billing address fields so user picks from saved addresses
                ['billing_address', 'billing_city', 'billing_state', 'billing_zip'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
            }

            // address select click handler (delegated)
            if (addressList) {
                addressList.addEventListener('click', function(ev) {
                    const target = ev.target.closest('.address-select');
                    if (!target) return;
                    const street = target.getAttribute('data-street') || '';
                    const number = target.getAttribute('data-number') || '';
                    const complement = target.getAttribute('data-complement') || '';
                    const neighborhood = target.getAttribute('data-neighborhood') || '';
                    let fullAddress = street;
                    if (number) fullAddress += ', ' + number;
                    if (complement) fullAddress += ' - ' + complement;
                    if (neighborhood) fullAddress += ' - ' + neighborhood;
                    document.getElementById('billing_address').value = fullAddress;
                    document.getElementById('billing_city').value = target.getAttribute('data-city') || '';
                    document.getElementById('billing_state').value = target.getAttribute('data-state') ||
                    '';
                    document.getElementById('billing_zip').value = target.getAttribute(
                        'data-postal-code') || '';
                    // mark radio
                    const radio = target.querySelector('input[type=radio]');
                    if (radio) radio.checked = true;
                });
            }

            // Save address via AJAX
            const saveAddressBtn = document.getElementById('saveAddressBtn');
            if (saveAddressBtn) {
                saveAddressBtn.addEventListener('click', function() {
                    const form = document.getElementById('newAddressForm');
                    const requiredFields = ['name', 'new_postal_code', 'new_street', 'new_number', 'new_neighborhood',
                        'new_city', 'new_state'
                    ];
                    let isValid = true;
                    requiredFields.forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (!input || !input.value.trim()) {
                            isValid = false;
                            if (input) input.classList.add('is-invalid');
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });
                    if (!isValid) {
                        showToast('Por favor, preencha todos os campos obrigatórios.', 'warning', 3500);
                        return;
                    }

                    const payload = {
                        name: form.querySelector('[name="name"]').value,
                        postal_code: form.querySelector('[name="postal_code"]').value,
                        street: form.querySelector('[name="street"]').value,
                        number: form.querySelector('[name="number"]').value,
                        complement: form.querySelector('[name="complement"]').value,
                        neighborhood: form.querySelector('[name="neighborhood"]').value,
                        city: form.querySelector('[name="city"]').value,
                        state: form.querySelector('[name="state"]').value,
                        is_default: form.querySelector('[name="is_default"]').checked ? 1 : 0
                    };

                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Salvando...';

                    fetch('{{ route('client.addresses.store') }}', {
                            method: 'POST',
                            body: JSON.stringify(payload),
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.success) {
                                // Show success toast and reload shortly after so user sees feedback
                                showToast('Endereço adicionado com sucesso!', 'success', 1200);
                                setTimeout(() => window.location.reload(), 1200);
                                return;
                            } else {
                                showToast('Erro ao salvar endereço: ' + (data.message || 'Erro desconhecido'), 'error', 4000);
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showToast('Erro ao salvar endereço. Tente novamente.', 'error', 4000);
                        })
                        .finally(() => {
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-save me-1"></i>Salvar Endereço';
                        });
                });
            }


            // Payment method change handling
            function handlePaymentMethodChange() {
                const selected = document.querySelector('input[name=payment_method_id]:checked');
                const gateway = selected ? selected.getAttribute('data-gateway') : null;
                const cardForm = document.getElementById('card-form');
                if (cardForm) cardForm.style.display = (gateway === 'card' || gateway === 'card_gateway') ?
                    'block' : 'none';
            }
            document.querySelectorAll('input[name=payment_method_id]').forEach(r => r.addEventListener('change',
                handlePaymentMethodChange));

            // Serialize payment card data before submit and validate terms
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function(e) {
                    const termsAccepted = document.getElementById('terms_accepted').checked;
                    if (!termsAccepted) {
                        e.preventDefault();
                        showToast('Você deve aceitar os termos de uso para continuar.', 'warning', 3500);
                        return;
                    }

                    const selected = document.querySelector('input[name=payment_method_id]:checked');
                    const gateway = selected ? selected.getAttribute('data-gateway') : null;
                    const paymentDataField = document.getElementById('payment_data');
                    if (gateway === 'card' || gateway === 'card_gateway') {
                        const card = {
                            card: {
                                holder: document.getElementById('card_holder').value,
                                number: document.getElementById('card_number').value,
                                expiry: document.getElementById('card_expiry').value,
                                cvv: document.getElementById('card_cvv').value,
                                installments: parseInt(document.getElementById('card_installments')
                                    .value) || 1
                            }
                        };
                        if (paymentDataField) paymentDataField.value = JSON.stringify(card);
                    } else if (paymentDataField) {
                        paymentDataField.value = '';
                    }
                });
            }

            // Initialize
            handlePaymentMethodChange();
        });
    </script>
@endpush
