@extends('layouts.blog')

@section('title', 'Meu Perfil - ' . $client->name)

@section('content')
    <!-- Banner com informações do cliente -->
    <div class="client-header-banner">
        <div class="container">
            <div class="row align-items-center py-4">
                <div class="col-md-2">
                    <div class="client-avatar">
                        <img src="{{ $client->avatar_url }}" alt="{{ $client->name }}" class="rounded-circle">
                    </div>
                </div>
                <div class="col-md-10">
                    <h1 class="h3 mb-1">Meu Perfil</h1>
                    <p class="text-muted mb-0">Gerencie suas informações pessoais</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Menu lateral do cliente -->
            <div class="col-lg-3">
                <div class="client-sidebar">
                    <div class="sidebar-header">
                        <h5><i class="fas fa-user-circle me-2"></i>Minha Conta</h5>
                    </div>
                    <nav class="client-nav">
                        <a href="{{ route('client.dashboard') }}" class="nav-item">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('client.profile') }}" class="nav-item active">
                            <i class="fas fa-user-edit"></i>
                            Meu Perfil
                        </a>
                        <a href="{{ route('client.addresses') }}" class="nav-item">
                            <i class="fas fa-map-marker-alt"></i>
                            Endereços
                        </a>
                        <a href="{{ route('client.preferences') }}" class="nav-item">
                            <i class="fas fa-cog"></i>
                            Preferências
                        </a>
                        <a href="{{ route('blog.index') }}" class="nav-item">
                            <i class="fas fa-home"></i>
                            Voltar ao Blog
                        </a>
                        <hr>
                        <form action="{{ route('client.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-item logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                Sair da Conta
                            </button>
                        </form>
                    </nav>
                </div>
            </div>

            <!-- Conteúdo principal -->
            <div class="col-lg-9">
                <div class="client-content">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Formulário de perfil -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5><i class="fas fa-user-edit me-2"></i>Informações Pessoais</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <!-- Avatar -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label">Foto do Perfil</label>
                                        <div class="avatar-upload-section">
                                            <div class="current-avatar">
                                                <img src="{{ $client->avatar_url }}" alt="{{ $client->name }}" class="rounded-circle" id="avatarPreview">
                                            </div>
                                            <div class="avatar-upload-controls">
                                                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                                <small class="text-muted">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</small>
                                                @error('avatar')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nome completo *</label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $client->name) }}" 
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">E-mail *</label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $client->email) }}" 
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Telefone</label>
                                        <input type="tel" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" 
                                               name="phone" 
                                               value="{{ old('phone', $client->phone) }}" 
                                               placeholder="(11) 99999-9999">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="birth_date" class="form-label">Data de nascimento</label>
                                        <input type="date" 
                                               class="form-control @error('birth_date') is-invalid @enderror" 
                                               id="birth_date" 
                                               name="birth_date" 
                                               value="{{ old('birth_date', $client->birth_date?->format('Y-m-d')) }}">
                                        @error('birth_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Gênero</label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                            <option value="">Selecione</option>
                                            <option value="masculino" {{ old('gender', $client->gender) === 'masculino' ? 'selected' : '' }}>Masculino</option>
                                            <option value="feminino" {{ old('gender', $client->gender) === 'feminino' ? 'selected' : '' }}>Feminino</option>
                                            <option value="outro" {{ old('gender', $client->gender) === 'outro' ? 'selected' : '' }}>Outro</option>
                                            <option value="nao_informar" {{ old('gender', $client->gender) === 'nao_informar' ? 'selected' : '' }}>Prefiro não informar</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="bio" class="form-label">Biografia</label>
                                        <textarea class="form-control @error('bio') is-invalid @enderror" 
                                                  id="bio" 
                                                  name="bio" 
                                                  rows="4" 
                                                  placeholder="Conte um pouco sobre você...">{{ old('bio', $client->bio) }}</textarea>
                                        @error('bio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Máximo 500 caracteres</div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Salvar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Alterar senha -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5><i class="fas fa-lock me-2"></i>Alterar Senha</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('client.password.update') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="current_password" class="form-label">Senha atual *</label>
                                        <input type="password" 
                                               class="form-control @error('current_password') is-invalid @enderror" 
                                               id="current_password" 
                                               name="current_password" 
                                               required>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="password" class="form-label">Nova senha *</label>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmar nova senha *</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               required>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key me-2"></i>Alterar Senha
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .client-header-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        margin-bottom: 0;
    }

    .client-avatar img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border: 3px solid rgba(255,255,255,0.3);
    }

    .client-sidebar {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .sidebar-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #dee2e6;
    }

    .sidebar-header h5 {
        margin: 0;
        color: #2d3748;
    }

    .client-nav {
        padding: 1rem 0;
    }

    .nav-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        color: #4a5568;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }

    .nav-item:hover {
        background: #f7fafc;
        color: #667eea;
        text-decoration: none;
    }

    .nav-item.active {
        background: #667eea;
        color: white;
    }

    .nav-item i {
        width: 20px;
        margin-right: 0.75rem;
    }

    .logout-btn {
        color: #e53e3e !important;
    }

    .logout-btn:hover {
        background: #fed7d7 !important;
        color: #c53030 !important;
    }

    .dashboard-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .dashboard-card .card-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #dee2e6;
    }

    .dashboard-card .card-header h5 {
        margin: 0;
        color: #2d3748;
    }

    .dashboard-card .card-body {
        padding: 1.5rem;
    }

    .avatar-upload-section {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .current-avatar img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border: 3px solid #e2e8f0;
    }

    .avatar-upload-controls {
        flex: 1;
    }

    @media (max-width: 768px) {
        .client-header-banner .row {
            text-align: center;
        }
        
        .client-sidebar {
            margin-bottom: 2rem;
        }

        .avatar-upload-section {
            flex-direction: column;
            text-align: center;
        }
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

    // Avatar preview
    $('#avatar').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatarPreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Bio character counter
    $('#bio').on('input', function() {
        const maxLength = 500;
        const currentLength = this.value.length;
        const remaining = maxLength - currentLength;
        
        let counterText = `${currentLength}/${maxLength} caracteres`;
        if (remaining < 0) {
            counterText += ` (${Math.abs(remaining)} acima do limite)`;
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
        
        $(this).siblings('.form-text').text(counterText);
    });
</script>
@endsection