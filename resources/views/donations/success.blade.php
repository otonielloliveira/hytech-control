@extends('layouts.app')

@section('title', 'Doação Confirmada!')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Header -->
            <div class="text-center mb-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h1 class="h2 text-success mb-3">Doação Confirmada!</h1>
                <p class="lead">
                    Muito obrigado pela sua contribuição! 
                    Sua doação nos ajuda a continuar oferecendo nossos serviços.
                </p>
            </div>

            <!-- Card de Confirmação -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt me-2"></i>Comprovante de Doação
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>ID da Doação:</strong>
                            <p class="mb-0 font-monospace">#{{ str_pad($donation->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>

                        <div class="col-md-6">
                            <strong>Data do Pagamento:</strong>
                            <p class="mb-0">{{ ($donation->payments->first()?->paid_at ?? $donation->paid_at)?->format('d/m/Y H:i:s') ?? 'Processando...' }}</p>
                        </div>

                        <div class="col-md-6">
                            <strong>Doador:</strong>
                            <p class="mb-0">{{ $donation->name }}</p>
                        </div>

                        <div class="col-md-6">
                            <strong>Email:</strong>
                            <p class="mb-0">{{ $donation->email }}</p>
                        </div>

                        @if($donation->phone)
                        <div class="col-md-6">
                            <strong>Telefone:</strong>
                            <p class="mb-0">{{ $donation->phone }}</p>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <strong>Valor Doado:</strong>
                            <p class="mb-0 h4 text-success">{{ $donation->formatted_amount }}</p>
                        </div>

                        <div class="col-md-6">
                            <strong>Método de Pagamento:</strong>
                            <p class="mb-0">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                @if($donation->payments->first())
                                    {{ ucfirst($donation->payments->first()->payment_method) }} 
                                    via {{ $donation->payments->first()->gateway }}
                                @else
                                    PIX
                                @endif
                            </p>
                        </div>

                        @if($donation->payments->first()?->transaction_id)
                        <div class="col-md-6">
                            <strong>ID da Transação:</strong>
                            <p class="mb-0 font-monospace small">{{ $donation->payments->first()->transaction_id }}</p>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <p class="mb-0">
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Confirmado
                                </span>
                            </p>
                        </div>

                        @if($donation->message)
                        <div class="col-12">
                            <strong>Sua Mensagem:</strong>
                            <p class="mb-0 fst-italic bg-light p-3 rounded">"{{ $donation->message }}"</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Próximos Passos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>O que acontece agora?
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-envelope text-primary mb-2" style="font-size: 2rem;"></i>
                            <h6>Confirmação por Email</h6>
                            <p class="text-muted small">Você receberá um email de confirmação em alguns minutos</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-heart text-danger mb-2" style="font-size: 2rem;"></i>
                            <h6>Agradecimento</h6>
                            <p class="text-muted small">Sua contribuição faz toda a diferença</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-rocket text-success mb-2" style="font-size: 2rem;"></i>
                            <h6>Melhorias</h6>
                            <p class="text-muted small">Usaremos sua doação para melhorar nossos serviços</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Depoimento de Agradecimento -->
            <div class="card bg-light mb-4">
                <div class="card-body text-center">
                    <blockquote class="blockquote mb-0">
                        <p class="mb-3">
                            "Cada doação recebida é um voto de confiança em nosso trabalho. 
                            Prometemos usar esses recursos de forma responsável para continuar 
                            oferecendo conteúdo de qualidade e serviços que façam a diferença."
                        </p>
                        <footer class="blockquote-footer">
                            <strong>Equipe HyTech Control</strong>
                        </footer>
                    </blockquote>
                </div>
            </div>

            <!-- Ações -->
            <div class="text-center">
                <div class="btn-group" role="group">
                    <a href="{{ route('blog.index') }}" class="btn btn-primary">
                        <i class="fas fa-home me-1"></i>Voltar ao Início
                    </a>
                    <a href="{{ route('donations.index') }}" class="btn btn-outline-success">
                        <i class="fas fa-heart me-1"></i>Fazer Nova Doação
                    </a>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Imprimir Comprovante
                    </button>
                </div>
            </div>

            <!-- Compartilhar -->
            <div class="text-center mt-4">
                <p class="text-muted mb-2">Ajude-nos a crescer compartilhando:</p>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary" onclick="shareOnFacebook()">
                        <i class="fab fa-facebook-f me-1"></i>Facebook
                    </button>
                    <button type="button" class="btn btn-outline-info" onclick="shareOnTwitter()">
                        <i class="fab fa-twitter me-1"></i>Twitter
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="shareOnWhatsApp()">
                        <i class="fab fa-whatsapp me-1"></i>WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Confetti effect on page load
document.addEventListener('DOMContentLoaded', function() {
    // Simples efeito de confete com CSS
    createConfetti();
});

function createConfetti() {
    const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7'];
    
    for (let i = 0; i < 50; i++) {
        const confetti = document.createElement('div');
        confetti.style.position = 'fixed';
        confetti.style.width = '10px';
        confetti.style.height = '10px';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.top = '-10px';
        confetti.style.zIndex = '9999';
        confetti.style.borderRadius = '50%';
        confetti.style.pointerEvents = 'none';
        
        document.body.appendChild(confetti);
        
        // Animação de queda
        confetti.animate([
            { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
            { transform: `translateY(${window.innerHeight + 20}px) rotate(360deg)`, opacity: 0 }
        ], {
            duration: Math.random() * 3000 + 2000,
            easing: 'linear',
            fill: 'forwards'
        }).addEventListener('finish', () => {
            confetti.remove();
        });
    }
}

function shareOnFacebook() {
    const url = encodeURIComponent(window.location.origin);
    const text = encodeURIComponent('Acabei de contribuir com um projeto incrível! Venha conhecer também.');
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${text}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.origin);
    const text = encodeURIComponent('Acabei de contribuir com um projeto incrível! 💝 #doacao #apoio');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
}

function shareOnWhatsApp() {
    const url = encodeURIComponent(window.location.origin);
    const text = encodeURIComponent(`Acabei de contribuir com um projeto incrível! 💝\n\nConheça também: ${window.location.origin}`);
    window.open(`https://wa.me/?text=${text}`, '_blank');
}
</script>
@endsection

@section('styles')
<style>
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-30px);
    }
    60% {
        transform: translateY(-15px);
    }
}

.fas.fa-check-circle {
    animation: bounce 2s infinite;
}

@media print {
    .btn-group,
    .btn,
    .text-center .mt-4 {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
}
</style>
@endsection