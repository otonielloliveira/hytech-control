@extends('layouts.app')

@section('title', 'Ajudar o Projeto')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="text-center mb-5">
                <div class="mb-3">
                    <i class="fas fa-heart text-danger" style="font-size: 3rem;"></i>
                </div>
                <h1 class="h2 mb-3">Ajudar o Projeto</h1>
                <p class="lead text-muted">
                    Sua contribuição nos ajuda a manter e melhorar nossos serviços. 
                    Qualquer valor é bem-vindo e faz a diferença!
                </p>
            </div>

            <!-- Alertas -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Card do Formulário -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-donate me-2"></i>Formulário de Doação
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('donations.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Nome -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Nome Completo *
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Digite seu nome completo"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email *
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="seu@email.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Telefone -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Telefone
                                </label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       placeholder="(11) 99999-9999">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Valor -->
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">
                                    <i class="fas fa-dollar-sign me-1"></i>Valor da Doação * (R$)
                                </label>
                                <input type="number" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" 
                                       name="amount" 
                                       value="{{ old('amount') }}" 
                                       min="1" 
                                       max="10000" 
                                       step="0.01" 
                                       placeholder="Ex: 25.00"
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Valores Sugeridos -->
                        <div class="mb-3">
                            <label class="form-label">Valores Sugeridos:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-success btn-sm amount-suggestion" data-amount="10">R$ 10</button>
                                <button type="button" class="btn btn-outline-success btn-sm amount-suggestion" data-amount="25">R$ 25</button>
                                <button type="button" class="btn btn-outline-success btn-sm amount-suggestion" data-amount="50">R$ 50</button>
                                <button type="button" class="btn btn-outline-success btn-sm amount-suggestion" data-amount="100">R$ 100</button>
                                <button type="button" class="btn btn-outline-success btn-sm amount-suggestion" data-amount="250">R$ 250</button>
                            </div>
                        </div>

                        <!-- Mensagem -->
                        <div class="mb-4">
                            <label for="message" class="form-label">
                                <i class="fas fa-comment me-1"></i>Mensagem (Opcional)
                            </label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" 
                                      name="message" 
                                      rows="3" 
                                      placeholder="Deixe uma mensagem para nós (opcional)">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Informações -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Como funciona:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Clique em "Gerar PIX" para criar sua doação</li>
                                <li>Escaneie o QR Code com o app do seu banco</li>
                                <li>O pagamento será confirmado automaticamente</li>
                                <li>Você receberá um email de confirmação</li>
                            </ul>
                        </div>

                        <!-- Botão -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-qrcode me-2"></i>Gerar PIX para Doação
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Por que doar? -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-question-circle me-2"></i>Por que sua doação é importante?
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-server text-primary mb-2" style="font-size: 2rem;"></i>
                            <h6>Manutenção</h6>
                            <p class="text-muted small">Servidores, domínio e infraestrutura</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-code text-success mb-2" style="font-size: 2rem;"></i>
                            <h6>Desenvolvimento</h6>
                            <p class="text-muted small">Novas funcionalidades e melhorias</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-graduation-cap text-info mb-2" style="font-size: 2rem;"></i>
                            <h6>Conteúdo</h6>
                            <p class="text-muted small">Criação de mais cursos e materiais</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Botões de valor sugerido
    const amountSuggestions = document.querySelectorAll('.amount-suggestion');
    const amountInput = document.getElementById('amount');
    
    amountSuggestions.forEach(button => {
        button.addEventListener('click', function() {
            const amount = this.getAttribute('data-amount');
            amountInput.value = amount;
            
            // Remove active class from all buttons
            amountSuggestions.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
        });
    });

    // Formatação do campo telefone
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 11) {
            value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (value.length >= 6) {
            value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else if (value.length >= 2) {
            value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
        }
        e.target.value = value;
    });
});
</script>
@endpush
@endsection