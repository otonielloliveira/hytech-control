@extends('layouts.app')

@section('title', 'Pagamento da Doação')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="text-center mb-4">
                <i class="fas fa-qrcode text-primary" style="font-size: 3rem;"></i>
                <h1 class="h2 mt-3">Finalize sua Doação</h1>
                <p class="text-muted">Escaneie o QR Code abaixo com o app do seu banco</p>
            </div>

            <div class="row">
                <!-- QR Code -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white text-center">
                            <h5 class="mb-0">
                                <i class="fas fa-mobile-alt me-2"></i>QR Code PIX
                            </h5>
                        </div>
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            @if($qrCode)
                                <div class="qr-code-container mb-3">
                                    {!! $qrCode !!}
                                </div>
                                
                                <!-- Código PIX para copiar -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Código PIX (Copia e Cola)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control text-center small" 
                                               id="pixCode" 
                                               value="{{ $payment->pix_code ?? '' }}" 
                                               readonly>
                                        <button class="btn btn-outline-primary" 
                                                type="button" 
                                                onclick="copyPixCode()"
                                                title="Copiar código PIX">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Status do pagamento -->
                                <div class="payment-status">
                                    <div class="alert alert-warning d-flex align-items-center" id="statusAlert">
                                        <div class="spinner-border spinner-border-sm me-2" role="status">
                                            <span class="visually-hidden">Verificando...</span>
                                        </div>
                                        <span id="statusText">Aguardando pagamento...</span>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Erro ao gerar QR Code. Tente novamente.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informações da Doação -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Detalhes da Doação
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <strong>Doador:</strong>
                                    <p class="mb-0">{{ $donation->name }}</p>
                                </div>
                                
                                <div class="col-12">
                                    <strong>Email:</strong>
                                    <p class="mb-0">{{ $donation->email }}</p>
                                </div>

                                @if($donation->phone)
                                <div class="col-12">
                                    <strong>Telefone:</strong>
                                    <p class="mb-0">{{ $donation->phone }}</p>
                                </div>
                                @endif

                                <div class="col-12">
                                    <strong>Valor:</strong>
                                    <p class="mb-0 h4 text-success">{{ $donation->formatted_amount }}</p>
                                </div>

                                @if($donation->message)
                                <div class="col-12">
                                    <strong>Mensagem:</strong>
                                    <p class="mb-0 fst-italic">"{{ $donation->message }}"</p>
                                </div>
                                @endif

                                <div class="col-12">
                                    <strong>Expira em:</strong>
                                    <p class="mb-0 text-warning">
                                        <i class="fas fa-clock me-1"></i>
                                        <span id="countdown">{{ $payment->expires_at ? $payment->expires_at->format('d/m/Y H:i:s') : $donation->expires_at->format('d/m/Y H:i:s') }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Instruções -->
                            <div class="mt-4">
                                <h6>Como pagar:</h6>
                                <ol class="small">
                                    <li>Abra o app do seu banco</li>
                                    <li>Escolha a opção PIX</li>
                                    <li>Escaneie o QR Code ou cole o código</li>
                                    <li>Confirme o pagamento</li>
                                </ol>
                            </div>

                            @if(app()->environment('local'))
                            <!-- Botão de teste apenas em desenvolvimento -->
                            <div class="mt-4">
                                <hr>
                                <div class="alert alert-info small">
                                    <strong>Modo de teste:</strong> Use o botão abaixo para simular o pagamento.
                                </div>
                                <a href="{{ route('donations.simulate', $donation->id) }}" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-flask me-1"></i>Simular Pagamento
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de ação -->
            <div class="text-center mt-4">
                <a href="{{ route('donations.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Nova Doação
                </a>
                <button type="button" class="btn btn-primary" onclick="checkPaymentStatus()">
                    <i class="fas fa-sync-alt me-1"></i>Verificar Pagamento
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let checkInterval;
let countdownInterval;

document.addEventListener('DOMContentLoaded', function() {
    // Iniciar verificação automática
    startPaymentCheck();
    
    // Iniciar countdown
    startCountdown();
});

function startPaymentCheck() {
    // Verificar status a cada 5 segundos
    checkInterval = setInterval(checkPaymentStatus, 5000);
}

function stopPaymentCheck() {
    if (checkInterval) {
        clearInterval(checkInterval);
    }
}

function checkPaymentStatus() {
    fetch(`{{ route('donations.status', $donation->id) }}`)
        .then(response => response.json())
        .then(data => {
            updateStatusDisplay(data);
            
            if (data.is_paid) {
                stopPaymentCheck();
                window.location.href = `{{ route('donations.success', $donation->id) }}`;
            } else if (data.is_expired) {
                stopPaymentCheck();
                showExpiredMessage();
            }
        })
        .catch(error => {
            console.error('Erro ao verificar status:', error);
        });
}

function updateStatusDisplay(data) {
    const statusAlert = document.getElementById('statusAlert');
    const statusText = document.getElementById('statusText');
    
    statusAlert.className = 'alert d-flex align-items-center';
    
    if (data.is_paid) {
        statusAlert.classList.add('alert-success');
        statusText.innerHTML = '<i class="fas fa-check me-2"></i>Pagamento confirmado!';
    } else if (data.is_expired) {
        statusAlert.classList.add('alert-danger');
        statusText.innerHTML = '<i class="fas fa-times me-2"></i>Pagamento expirado';
    } else {
        statusAlert.classList.add('alert-warning');
        statusText.innerHTML = '<div class="spinner-border spinner-border-sm me-2" role="status"></div>Aguardando pagamento...';
    }
}

function showExpiredMessage() {
    const statusAlert = document.getElementById('statusAlert');
    statusAlert.className = 'alert alert-danger';
    statusAlert.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Esta doação expirou. <a href="{{ route('donations.index') }}" class="alert-link">Faça uma nova doação</a>';
}

function copyPixCode() {
    const pixCode = document.getElementById('pixCode');
    pixCode.select();
    pixCode.setSelectionRange(0, 99999);
    
    navigator.clipboard.writeText(pixCode.value).then(function() {
        // Feedback visual
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
        }, 2000);
        
        // Toast notification se disponível
        if (typeof bootstrap !== 'undefined') {
            showToast('Código PIX copiado!', 'success');
        }
    });
}

function startCountdown() {
    const expiresAt = new Date('{{ ($payment->expires_at ?? $donation->expires_at)->toISOString() }}');
    const countdownElement = document.getElementById('countdown');
    
    countdownInterval = setInterval(() => {
        const now = new Date();
        const timeLeft = expiresAt - now;
        
        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            countdownElement.textContent = 'Expirado';
            showExpiredMessage();
            return;
        }
        
        const minutes = Math.floor(timeLeft / 60000);
        const seconds = Math.floor((timeLeft % 60000) / 1000);
        
        countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    }, 1000);
}

function showToast(message, type = 'info') {
    // Implementação simples de toast
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Limpar intervalos ao sair da página
window.addEventListener('beforeunload', function() {
    stopPaymentCheck();
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
});
</script>
@endpush

@push('styles')
<style>
.qr-code-container svg {
    max-width: 100%;
    height: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    background: white;
}

@media (max-width: 768px) {
    .qr-code-container svg {
        max-width: 250px;
    }
}
</style>
@endpush
@endsection