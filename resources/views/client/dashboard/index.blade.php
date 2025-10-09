@extends('layouts.blog')

@section('title', 'Meu Painel - ' . $client->name)

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
                <div class="col-md-8">
                    <h1 class="h3 mb-1">Bem-vindo, {{ $client->name }}!</h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-envelope me-2"></i>{{ $client->email }}
                        @if($client->phone)
                            <span class="ms-3">
                                <i class="fas fa-phone me-2"></i>{{ $client->phone }}
                            </span>
                        @endif
                    </p>
                    @if($client->last_login_at)
                        <small class="text-muted">
                            Último acesso: {{ $client->last_login_at->format('d/m/Y \à\s H:i') }}
                        </small>
                    @endif
                </div>
                <div class="col-md-2 text-end">
                    <div class="client-actions">
                        <a href="{{ route('client.profile') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Editar Perfil
                        </a>
                    </div>
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
                        <a href="{{ route('client.dashboard') }}" class="nav-item active">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('client.profile') }}" class="nav-item">
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
                    <!-- Estatísticas rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="stats-info">
                                    <h3>{{ $client->addresses->count() }}</h3>
                                    <p>Endereços cadastrados</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="stats-info">
                                    <h3>{{ $client->created_at->format('M/Y') }}</h3>
                                    <p>Membro desde</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="stats-info">
                                    <h3>Ativo</h3>
                                    <p>Status da conta</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações rápidas -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-user me-2"></i>Informações do Perfil</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-row">
                                        <span class="label">Nome:</span>
                                        <span class="value">{{ $client->name }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="label">E-mail:</span>
                                        <span class="value">{{ $client->email }}</span>
                                    </div>
                                    @if($client->phone)
                                    <div class="info-row">
                                        <span class="label">Telefone:</span>
                                        <span class="value">{{ $client->phone }}</span>
                                    </div>
                                    @endif
                                    @if($client->birth_date)
                                    <div class="info-row">
                                        <span class="label">Nascimento:</span>
                                        <span class="value">{{ $client->birth_date->format('d/m/Y') }}</span>
                                    </div>
                                    @endif
                                    <div class="mt-3">
                                        <a href="{{ route('client.profile') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit me-1"></i>Editar Perfil
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-map-marker-alt me-2"></i>Endereços</h5>
                                </div>
                                <div class="card-body">
                                    @if($client->addresses->count() > 0)
                                        @foreach($client->addresses->take(2) as $address)
                                        <div class="address-item">
                                            <strong>{{ $address->name }}</strong>
                                            @if($address->is_default)
                                                <span class="badge bg-primary ms-2">Padrão</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">{{ $address->full_address }}</small>
                                        </div>
                                        @endforeach
                                        @if($client->addresses->count() > 2)
                                            <small class="text-muted">e mais {{ $client->addresses->count() - 2 }} endereços...</small>
                                        @endif
                                    @else
                                        <p class="text-muted">Nenhum endereço cadastrado.</p>
                                    @endif
                                    <div class="mt-3">
                                        <a href="{{ route('client.addresses') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>Gerenciar Endereços
                                        </a>
                                    </div>
                                </div>
                            </div>
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

    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .stats-info h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
    }

    .stats-info p {
        margin: 0;
        color: #718096;
        font-size: 0.9rem;
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

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f3f4;
    }

    .info-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .info-row .label {
        font-weight: 600;
        color: #4a5568;
    }

    .info-row .value {
        color: #2d3748;
    }

    .address-item {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f3f4;
    }

    .address-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .client-header-banner .row {
            text-align: center;
        }
        
        .client-sidebar {
            margin-bottom: 2rem;
        }
    }
</style>
@endsection