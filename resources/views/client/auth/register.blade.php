@extends('layouts.blog')

@section('title', 'Cadastro - Cliente')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="auth-card">
                    <div class="auth-header">
                        <h2><i class="fas fa-user-plus me-2"></i>Criar nova conta</h2>
                        <p class="text-muted">Faça parte da nossa comunidade</p>
                    </div>
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('client.register') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label">Nome completo *</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">E-mail *</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefone</label>
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
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Senha *</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar senha *</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Data de nascimento</label>
                                <input type="date" 
                                       class="form-control @error('birth_date') is-invalid @enderror" 
                                       id="birth_date" 
                                       name="birth_date" 
                                       value="{{ old('birth_date') }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gênero</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="">Selecione</option>
                                    <option value="masculino" {{ old('gender') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="feminino" {{ old('gender') === 'feminino' ? 'selected' : '' }}>Feminino</option>
                                    <option value="outro" {{ old('gender') === 'outro' ? 'selected' : '' }}>Outro</option>
                                    <option value="nao_informar" {{ old('gender') === 'nao_informar' ? 'selected' : '' }}>Prefiro não informar</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Criar conta
                            </button>
                        </div>
                    </form>
                    
                    <div class="auth-footer">
                        <p class="text-center">
                            Já tem uma conta? 
                            <a href="{{ route('client.login') }}" class="text-decoration-none">
                                Faça login aqui
                            </a>
                        </p>
                        <p class="text-center">
                            <a href="{{ route('blog.index') }}" class="text-muted text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Voltar ao blog
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .auth-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-header h2 {
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .auth-footer {
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        border: none;
        padding: 0.75rem;
        font-weight: 600;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        transform: translateY(-1px);
    }
</style>
@endsection

@section('scripts')
<script>
    // Phone mask
    $('#phone').on('input', function() {
        let value = this.value.replace(/\D/g, '');
        value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
        value = value.replace(/(\d)(\d{4})$/, '$1-$2');
        this.value = value;
    });
</script>
@endsection