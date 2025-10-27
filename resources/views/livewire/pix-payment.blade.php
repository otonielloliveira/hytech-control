<div class="pix-payment-container">
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($pixData)
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-qrcode me-2"></i>
                    Pagamento via PIX
                </h5>
            </div>
            <div class="card-body text-center">
                <!-- QR Code -->
                <div class="mb-4">
                    <img src="{{ $pixData['qr_code_base64'] }}" 
                         alt="QR Code PIX" 
                         class="img-fluid rounded shadow-sm"
                         style="max-width: 300px;">
                </div>

                <!-- Instruções -->
                <div class="alert alert-info mb-4">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>
                        Como pagar:
                    </h6>
                    <ol class="text-start mb-0">
                        <li>Abra o app do seu banco</li>
                        <li>Escolha a opção <strong>Pix QR Code</strong></li>
                        <li>Escaneie o QR Code acima</li>
                        <li>Confirme o pagamento</li>
                    </ol>
                </div>

                <!-- Código PIX Copia e Cola -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Ou copie o código PIX:</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control font-monospace text-truncate" 
                               value="{{ $pixData['payload'] }}" 
                               readonly
                               id="pixCodeInput">
                        <button class="btn btn-primary" 
                                type="button" 
                                wire:click="copyPixCode"
                                onclick="copyToClipboard()">
                            <i class="fas fa-copy me-1"></i>
                            Copiar
                        </button>
                    </div>
                    <small class="text-muted">Cole este código no app do seu banco na opção "Pix Copia e Cola"</small>
                </div>

                <!-- Informações adicionais (PIX Manual) -->
                @if (isset($pixData['pix_key']) && isset($pixData['beneficiary_name']))
                    <div class="border-top pt-3 mt-3">
                        <h6 class="text-muted mb-3">Informações do Beneficiário:</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Nome:</strong><br>
                                {{ $pixData['beneficiary_name'] }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Chave PIX:</strong><br>
                                <code>{{ $pixData['pix_key'] }}</code>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Valor -->
                @if (isset($model->amount))
                    <div class="mt-4 p-3 bg-light rounded">
                        <h4 class="mb-0">
                            Valor: <span class="text-success fw-bold">R$ {{ number_format($model->amount, 2, ',', '.') }}</span>
                        </h4>
                    </div>
                @elseif (isset($model->total))
                    <div class="mt-4 p-3 bg-light rounded">
                        <h4 class="mb-0">
                            Valor: <span class="text-success fw-bold">R$ {{ number_format($model->total, 2, ',', '.') }}</span>
                        </h4>
                    </div>
                @elseif (isset($model->paid_amount))
                    <div class="mt-4 p-3 bg-light rounded">
                        <h4 class="mb-0">
                            Valor: <span class="text-success fw-bold">R$ {{ number_format($model->paid_amount, 2, ',', '.') }}</span>
                        </h4>
                    </div>
                @endif

                <!-- Aviso importante -->
                <div class="alert alert-warning mt-4 mb-0">
                    <small>
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Importante:</strong> Após realizar o pagamento, aguarde a confirmação. 
                        O processamento pode levar alguns minutos.
                    </small>
                </div>
            </div>
        </div>
    @else
        <!-- Botão para gerar código PIX -->
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-qrcode fa-4x text-muted"></i>
                </div>
                <h5 class="mb-3">Pagamento via PIX</h5>
                <p class="text-muted mb-4">
                    Gere seu código PIX para realizar o pagamento de forma rápida e segura.
                </p>
                <button class="btn btn-success btn-lg" 
                        wire:click="generatePixCode" 
                        wire:loading.attr="disabled"
                        wire:target="generatePixCode">
                    <span wire:loading.remove wire:target="generatePixCode">
                        <i class="fas fa-qrcode me-2"></i>
                        Gerar Código PIX
                    </span>
                    <span wire:loading wire:target="generatePixCode">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Gerando...
                    </span>
                </button>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function copyToClipboard() {
    const input = document.getElementById('pixCodeInput');
    input.select();
    input.setSelectionRange(0, 99999); // Para mobile
    
    try {
        document.execCommand('copy');
        // Visual feedback
        const btn = event.currentTarget;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-1"></i> Copiado!';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
        }, 2000);
    } catch (err) {
        console.error('Erro ao copiar:', err);
    }
}

// Listener para o evento do Livewire
document.addEventListener('livewire:initialized', () => {
    Livewire.on('copy-to-clipboard', (event) => {
        const text = event.text;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text);
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.pix-payment-container .font-monospace {
    font-size: 0.85rem;
}

.pix-payment-container .card {
    border: none;
}

.pix-payment-container .alert ol {
    padding-left: 1.2rem;
    margin-bottom: 0;
}

.pix-payment-container .alert ol li {
    margin-bottom: 0.5rem;
}

.pix-payment-container .alert ol li:last-child {
    margin-bottom: 0;
}
</style>
@endpush
