@extends('layouts.blog')

@section('title', 'Pedido Realizado com Sucesso - Loja')

@section('styles')
    <style>
        body {
            background-color: #ebebeb;
        }

        .success-container {
            max-width: 900px;
            margin: 2rem auto;
        }

        .card {
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,.1);
            margin-bottom: 1rem;
            padding: 1.5rem;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #00a650;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .success-icon i {
            font-size: 40px;
            color: white;
        }

        .order-number {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .order-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background: #d1ecf1;
            color: #0c5460;
        }

        .order-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .detail-item {
            padding: 1rem;
            background: #f8f8f8;
            border-radius: 6px;
        }

        .detail-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .pix-container {
            background: linear-gradient(135deg, #00a650 0%, #008c43 100%);
            border-radius: 6px;
            padding: 2rem;
            color: white;
            text-align: center;
        }

        .qr-code-wrapper {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            display: inline-block;
            margin: 1rem 0;
        }

        .qr-code-wrapper img {
            display: block;
            max-width: 250px;
            height: auto;
        }

        .pix-code-box {
            background: rgba(255,255,255,0.2);
            border: 1px dashed rgba(255,255,255,0.5);
            border-radius: 6px;
            padding: 1rem;
            margin: 1rem 0;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
        }

        .copy-button {
            background: white;
            color: #00a650;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .copy-button:hover {
            background: #f0f0f0;
            transform: translateY(-1px);
        }

        .copy-button.copied {
            background: #333;
            color: white;
        }

        .items-list {
            border-top: 1px solid #e6e6e6;
            padding-top: 1rem;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .btn-primary {
            background: #3483fa;
            color: white;
            padding: 14px 32px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: #2968c8;
            color: white;
            text-decoration: none;
        }

        .btn-secondary {
            color: #3483fa;
            text-decoration: none;
        }

        .btn-secondary:hover {
            text-decoration: underline;
        }

        .alert-info {
            background: #e7f3ff;
            border: 1px solid #3483fa;
            border-radius: 6px;
            padding: 1rem;
            color: #1e6bb8;
        }
    </style>
@endsection

@section('content')
    <div class="container success-container">
        <!-- Mensagem de Sucesso -->
        <div class="card text-center">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="order-number">Pedido realizado com sucesso!</h1>
            <p style="color: #666; margin-bottom: 0;">
                Pedido <strong>#{{ $order->order_number }}</strong>
            </p>
        </div>

        @if(session('payment_result'))
            @php 
                $paymentResult = session('payment_result');
                $paymentMethod = $paymentResult['payment_method'] ?? null;
            @endphp

            <!-- PIX Payment -->
            @if($paymentMethod === 'pix')
                <div class="card">
                    @php
                        $isPixManual = $paymentResult['is_pix_manual'] ?? false;
                        $pixManualData = $paymentResult['pix_manual_data'] ?? null;
                    @endphp

                    @if($isPixManual && $pixManualData)
                        <!-- PIX Manual - Mostrar dados da chave -->
                        <div class="pix-container">
                            <h2 style="margin-bottom: 0.5rem; font-size: 24px;">
                                <i class="fas fa-qrcode me-2"></i>Pagamento via PIX Manual
                            </h2>
                            <p style="margin-bottom: 1.5rem; opacity: 0.9;">
                                Use os dados abaixo para fazer a transferência PIX
                            </p>

                            <!-- Informações do PIX -->
                            <div style="background: rgba(255,255,255,0.15); border-radius: 8px; padding: 1.5rem; margin: 1.5rem 0;">
                                <div style="margin-bottom: 1rem;">
                                    <p style="font-size: 13px; opacity: 0.8; margin-bottom: 0.25rem;">Tipo de Chave:</p>
                                    <p style="font-size: 18px; font-weight: 600; margin: 0;">{{ $pixManualData['pix_key_type'] }}</p>
                                </div>

                                <div style="margin-bottom: 1rem;">
                                    <p style="font-size: 13px; opacity: 0.8; margin-bottom: 0.25rem;">Chave PIX:</p>
                                    <div style="background: rgba(255,255,255,0.2); border: 1px dashed rgba(255,255,255,0.5); border-radius: 6px; padding: 1rem; word-break: break-all; font-family: monospace; font-size: 16px; font-weight: 600;">
                                        <span id="pixKeyValue">{{ $pixManualData['pix_key'] }}</span>
                                    </div>
                                    <button class="copy-button" onclick="copyPixKey()" style="margin-top: 0.75rem;">
                                        <i class="fas fa-copy me-2"></i>
                                        <span id="copyKeyButtonText">Copiar Chave PIX</span>
                                    </button>
                                </div>

                                <div style="margin-bottom: 0;">
                                    <p style="font-size: 13px; opacity: 0.8; margin-bottom: 0.25rem;">Beneficiário:</p>
                                    <p style="font-size: 18px; font-weight: 600; margin: 0;">{{ $pixManualData['beneficiary_name'] }}</p>
                                </div>
                            </div>

                            <div style="background: rgba(255,255,255,0.1); border-radius: 6px; padding: 1rem; margin-top: 1.5rem;">
                                <p style="font-size: 13px; opacity: 0.9; margin: 0;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Após realizar a transferência, seu pedido será processado em até 24 horas úteis.
                                </p>
                            </div>

                            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.2);">
                                <p style="font-size: 13px; opacity: 0.8; margin: 0;">
                                    💰 Valor a transferir: <strong style="font-size: 20px;">R$ {{ number_format($paymentResult['amount'], 2, ',', '.') }}</strong>
                                </p>
                            </div>
                        </div>
                    @else
                        <!-- PIX com QR Code (gateways automáticos) -->
                        <div class="pix-container">
                            <h2 style="margin-bottom: 0.5rem; font-size: 24px;">
                                <i class="fas fa-qrcode me-2"></i>Pagamento via PIX
                            </h2>
                            <p style="margin-bottom: 1.5rem; opacity: 0.9;">
                                Escaneie o QR Code ou copie o código abaixo
                            </p>

                            @php
                                $qrCodeImage = null;
                                $pixCode = null;

                                // Tentar obter QR Code de diferentes fontes
                                if (isset($paymentResult['qr_code']['qr_code_image'])) {
                                    $qrCodeImage = $paymentResult['qr_code']['qr_code_image'];
                                } elseif (isset($paymentResult['payment']['qr_code_base64'])) {
                                    $qrCodeImage = 'data:image/png;base64,' . $paymentResult['payment']['qr_code_base64'];
                                }

                                // Tentar obter código PIX
                                if (isset($paymentResult['qr_code']['qr_code_text'])) {
                                    $pixCode = $paymentResult['qr_code']['qr_code_text'];
                                } elseif (isset($paymentResult['payment']['pix_code'])) {
                                    $pixCode = $paymentResult['payment']['pix_code'];
                                }
                            @endphp

                            @if($qrCodeImage)
                                <div class="qr-code-wrapper">
                                    <img src="{{ $qrCodeImage }}" alt="QR Code PIX" id="pixQrCode">
                                </div>
                            @endif

                            @if($pixCode)
                                <div>
                                    <p style="font-size: 14px; margin-bottom: 0.5rem; opacity: 0.9;">
                                        Código PIX (Copia e Cola):
                                    </p>
                                    <div class="pix-code-box" id="pixCode">{{ $pixCode }}</div>
                                    <button class="copy-button" onclick="copyPixCode()">
                                        <i class="fas fa-copy me-2"></i>
                                        <span id="copyButtonText">Copiar código PIX</span>
                                    </button>
                                </div>
                            @endif

                            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.2);">
                                <p style="font-size: 13px; opacity: 0.8; margin: 0;">
                                    ⏱️ O pagamento será confirmado automaticamente após a aprovação
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Boleto Payment -->
            @if(in_array($paymentMethod, ['boleto', 'bank_slip']))
                <div class="card">
                    <div class="alert-info">
                        <h3 style="margin-bottom: 0.5rem;">
                            <i class="fas fa-barcode me-2"></i>Pagamento via Boleto Bancário
                        </h3>
                        <p style="margin-bottom: 1rem;">
                            Seu boleto foi gerado com sucesso! Clique no botão abaixo para visualizar e imprimir.
                        </p>
                        @if(isset($paymentResult['payment']['checkout_url']) || isset($paymentResult['bank_slip_url']))
                            <a href="{{ $paymentResult['payment']['checkout_url'] ?? $paymentResult['bank_slip_url'] }}" 
                               target="_blank" 
                               class="btn-primary">
                                <i class="fas fa-download me-2"></i>Visualizar Boleto
                            </a>
                        @endif
                        <p style="margin-top: 1rem; margin-bottom: 0; font-size: 13px;">
                            ℹ️ O prazo de compensação é de até 2 dias úteis
                        </p>
                    </div>
                </div>
            @endif

            <!-- Credit Card Payment -->
            @if(in_array($paymentMethod, ['card', 'credit_card']))
                <div class="card">
                    <div class="alert-info">
                        <h3 style="margin-bottom: 0.5rem;">
                            <i class="fas fa-credit-card me-2"></i>Pagamento via Cartão de Crédito
                        </h3>
                        <p style="margin-bottom: 0;">
                            Seu pagamento está sendo processado. Você receberá a confirmação por e-mail em breve.
                        </p>
                    </div>
                </div>
            @endif
        @endif

        <!-- Detalhes do Pedido -->
        <div class="card">
            <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 1rem;">Detalhes do pedido</h2>
            
            <div class="order-detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Pedido</div>
                    <div class="detail-value">#{{ $order->order_number }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Data</div>
                    <div class="detail-value">{{ $order->created_at->format('d/m/Y') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="order-status status-processing">Em processamento</span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Total</div>
                    <div class="detail-value" style="color: #00a650;">
                        R$ {{ number_format($order->total, 2, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="items-list">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 1rem;">Itens do pedido</h3>
                @foreach($order->items as $item)
                    <div class="item-row">
                        <span>
                            <strong>{{ $item->product_name }}</strong>
                            <small style="color: #666; display: block;">Quantidade: {{ $item->quantity }}</small>
                        </span>
                        <span style="font-weight: 600;">
                            R$ {{ number_format($item->total, 2, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Ações -->
        <div class="card text-center">
            <a href="{{ route('store.index') }}" class="btn-primary">
                <i class="fas fa-shopping-bag me-2"></i>Continuar comprando
            </a>
            <div style="margin-top: 1rem;">
                <a href="{{ route('blog.index') }}" class="btn-secondary">
                    <i class="fas fa-home me-1"></i>Voltar ao início
                </a>
            </div>
        </div>
    </div>

    <script>
        function copyPixKey() {
            const pixKey = document.getElementById('pixKeyValue').textContent;
            const button = event.target.closest('.copy-button');
            const buttonText = document.getElementById('copyKeyButtonText');
            
            navigator.clipboard.writeText(pixKey).then(() => {
                button.classList.add('copied');
                buttonText.innerHTML = '<i class="fas fa-check me-2"></i>Chave copiada!';
                
                setTimeout(() => {
                    button.classList.remove('copied');
                    buttonText.innerHTML = '<i class="fas fa-copy me-2"></i>Copiar Chave PIX';
                }, 3000);
            }).catch(err => {
                console.error('Erro ao copiar:', err);
                alert('Erro ao copiar chave. Por favor, copie manualmente.');
            });
        }

        function copyPixCode() {
            const pixCode = document.getElementById('pixCode').textContent;
            const button = document.querySelector('.copy-button');
            const buttonText = document.getElementById('copyButtonText');
            
            navigator.clipboard.writeText(pixCode).then(() => {
                button.classList.add('copied');
                buttonText.innerHTML = '<i class="fas fa-check me-2"></i>Código copiado!';
                
                setTimeout(() => {
                    button.classList.remove('copied');
                    buttonText.innerHTML = '<i class="fas fa-copy me-2"></i>Copiar código PIX';
                }, 3000);
            }).catch(err => {
                console.error('Erro ao copiar:', err);
                alert('Erro ao copiar código. Por favor, copie manualmente.');
            });
        }

        // Sistema de verificação em tempo real do status do pagamento
        @if(session('payment_result'))
            @php
                $paymentResult = session('payment_result');
                $paymentMethod = $paymentResult['payment_method'] ?? null;
                $transactionId = $paymentResult['payment']['transaction_id'] ?? 
                               $paymentResult['payment']['id'] ?? 
                               $paymentResult['transaction_id'] ?? null;
                $isPixManual = $paymentResult['is_pix_manual'] ?? false;
            @endphp

            @if(in_array($paymentMethod, ['pix', 'boleto', 'bank_slip']) && $transactionId && !$isPixManual)
                let checkInterval = null;
                let checkAttempts = 0;
                const maxAttempts = 240; // 20 minutos (5 segundos * 240)
                const orderId = {{ $order->id }};
                const transactionId = '{{ $transactionId }}';

                function checkPaymentStatus() {
                    checkAttempts++;

                    fetch(`/api/payment/status?order_id=${orderId}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Payment status:', data);

                            if (data.success) {
                                const status = data.payment_status;
                                const orderStatus = data.order_status;

                                // Pagamento aprovado
                                if (status === 'approved' || status === 'paid') {
                                    clearInterval(checkInterval);
                                    showPaymentConfirmation();
                                    updateOrderStatus('Pago', 'success');
                                }
                                // Pagamento rejeitado
                                else if (status === 'rejected' || status === 'failed' || status === 'cancelled') {
                                    clearInterval(checkInterval);
                                    showPaymentError(data.message || 'Pagamento não aprovado');
                                    updateOrderStatus('Falha no pagamento', 'danger');
                                }
                                // Continua verificando se ainda está pendente
                                else if (checkAttempts >= maxAttempts) {
                                    clearInterval(checkInterval);
                                    console.log('Tempo limite de verificação atingido');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao verificar status do pagamento:', error);
                        });
                }

                function showPaymentConfirmation() {
                    // Remove o container de PIX/Boleto
                    const paymentContainer = document.querySelector('.pix-container') || 
                                           document.querySelector('.alert-info');
                    if (paymentContainer) {
                        paymentContainer.parentElement.innerHTML = `
                            <div class="alert alert-success" style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 6px; padding: 1.5rem;">
                                <div style="text-align: center;">
                                    <div style="width: 60px; height: 60px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                        <i class="fas fa-check" style="font-size: 30px; color: white;"></i>
                                    </div>
                                    <h3 style="color: #155724; margin-bottom: 0.5rem;">
                                        <i class="fas fa-check-circle me-2"></i>Pagamento Confirmado!
                                    </h3>
                                    <p style="color: #155724; margin-bottom: 0; font-size: 16px;">
                                        Seu pagamento foi aprovado e seu pedido está sendo processado.
                                    </p>
                                </div>
                            </div>
                        `;
                    }

                    // Mostra notificação de sucesso
                    showToast('Pagamento confirmado com sucesso! 🎉', 'success');
                }

                function showPaymentError(message) {
                    const paymentContainer = document.querySelector('.pix-container') || 
                                           document.querySelector('.alert-info');
                    if (paymentContainer) {
                        paymentContainer.parentElement.innerHTML = `
                            <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 6px; padding: 1.5rem;">
                                <h3 style="color: #721c24; margin-bottom: 0.5rem;">
                                    <i class="fas fa-times-circle me-2"></i>Problema no Pagamento
                                </h3>
                                <p style="color: #721c24; margin-bottom: 0;">
                                    ${message}
                                </p>
                            </div>
                        `;
                    }
                }

                function updateOrderStatus(statusText, statusType) {
                    const statusElement = document.querySelector('.order-status');
                    if (statusElement) {
                        statusElement.className = `order-status status-${statusType}`;
                        statusElement.textContent = statusText;
                    }
                }

                function showToast(message, type = 'info') {
                    // Cria elemento de toast se não existir
                    let toastContainer = document.getElementById('toast-container');
                    if (!toastContainer) {
                        toastContainer = document.createElement('div');
                        toastContainer.id = 'toast-container';
                        toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
                        document.body.appendChild(toastContainer);
                    }

                    const toast = document.createElement('div');
                    toast.className = `alert alert-${type}`;
                    toast.style.cssText = 'min-width: 300px; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
                    toast.innerHTML = `
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span>${message}</span>
                            <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
                        </div>
                    `;

                    toastContainer.appendChild(toast);

                    // Remove automaticamente após 5 segundos
                    setTimeout(() => {
                        toast.remove();
                    }, 5000);
                }

                // Inicia a verificação a cada 5 segundos
                checkInterval = setInterval(checkPaymentStatus, 5000);

                // Primeira verificação imediata
                checkPaymentStatus();

                // Adiciona indicador visual de que está verificando
                @if($paymentMethod === 'pix')
                    const pixContainer = document.querySelector('.pix-container');
                    if (pixContainer) {
                        const statusIndicator = document.createElement('div');
                        statusIndicator.id = 'payment-status-indicator';
                        statusIndicator.style.cssText = 'margin-top: 1rem; padding: 0.75rem; background: rgba(255,255,255,0.1); border-radius: 6px; font-size: 13px;';
                        statusIndicator.innerHTML = '<i class="fas fa-sync fa-spin me-2"></i>Verificando pagamento automaticamente...';
                        pixContainer.appendChild(statusIndicator);
                    }
                @endif
            @endif
        @endif
    </script>
@endsection