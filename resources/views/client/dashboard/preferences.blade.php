@extends('layouts.blog')

@section('title', 'Preferências - ' . $client->name)

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
                    <h1 class="h3 mb-1">Preferências</h1>
                    <p class="text-muted mb-0">Configure suas preferências de notificações e comunicação</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Menu lateral do cliente -->
            @include('client.dashboard.partial-menu')

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

                    <!-- Preferências de Notificação -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5><i class="fas fa-bell me-2"></i>Preferências de Notificação</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('client.preferences.update') }}" method="POST">
                                @csrf
                                
                                <div class="preferences-section">
                                    <h6 class="section-title">
                                        <i class="fas fa-envelope me-2"></i>
                                        Comunicações por E-mail
                                    </h6>
                                    <p class="section-description">Configure quais e-mails você deseja receber</p>
                                    
                                    <div class="preference-item">
                                        <div class="preference-content">
                                            <div class="preference-info">
                                                <h6>Newsletter</h6>
                                                <p>Receber newsletter com os últimos posts e novidades do blog</p>
                                            </div>
                                            <div class="preference-control">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           id="newsletter" 
                                                           name="newsletter" 
                                                           value="1"
                                                           {{ ($client->preferences['newsletter'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="newsletter"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="preference-item">
                                        <div class="preference-content">
                                            <div class="preference-info">
                                                <h6>Notificações de E-mail</h6>
                                                <p>Receber e-mails sobre atividades da sua conta e atualizações importantes</p>
                                            </div>
                                            <div class="preference-control">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           id="email_notifications" 
                                                           name="email_notifications" 
                                                           value="1"
                                                           {{ ($client->preferences['email_notifications'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="email_notifications"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="preference-item">
                                        <div class="preference-content">
                                            <div class="preference-info">
                                                <h6>Notificações de Enquetes</h6>
                                                <p>Receber avisos sobre novas enquetes e resultados de votações</p>
                                            </div>
                                            <div class="preference-control">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           id="poll_notifications" 
                                                           name="poll_notifications" 
                                                           value="1"
                                                           {{ ($client->preferences['poll_notifications'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="poll_notifications"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="preference-item">
                                        <div class="preference-content">
                                            <div class="preference-info">
                                                <h6>Atualizações de Petições</h6>
                                                <p>Receber atualizações sobre petições que você assinou</p>
                                            </div>
                                            <div class="preference-control">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           id="petition_updates" 
                                                           name="petition_updates" 
                                                           value="1"
                                                           {{ ($client->preferences['petition_updates'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="petition_updates"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="preferences-section mt-4">
                                    <h6 class="section-title">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Informações Importantes
                                    </h6>
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-shield-alt me-2"></i>Privacidade e Dados</h6>
                                        <ul class="mb-0">
                                            <li>Seus dados pessoais são protegidos de acordo com a LGPD</li>
                                            <li>Você pode alterar essas preferências a qualquer momento</li>
                                            <li>E-mails importantes sobre segurança sempre serão enviados</li>
                                            <li>Para cancelar a newsletter, você também pode usar o link de descadastro nos e-mails</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-save me-2"></i>Salvar Preferências
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Informações da Conta -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5><i class="fas fa-user-cog me-2"></i>Informações da Conta</h5>
                        </div>
                        <div class="card-body">
                            <div class="account-info">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <i class="fas fa-calendar-plus"></i>
                                        <div>
                                            <h6>Membro desde</h6>
                                            <p>{{ $client->created_at->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <div>
                                            <h6>Último acesso</h6>
                                            <p>{{ $client->last_login_at ? $client->last_login_at->format('d/m/Y H:i') : 'Primeiro acesso' }}</p>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <div>
                                            <h6>Endereços cadastrados</h6>
                                            <p>{{ $client->addresses->count() }} {{ $client->addresses->count() === 1 ? 'endereço' : 'endereços' }}</p>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-check-circle"></i>
                                        <div>
                                            <h6>Status da conta</h6>
                                            <p><span class="badge bg-success">Ativa</span></p>
                                        </div>
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

    .preferences-section {
        margin-bottom: 2rem;
    }

    .section-title {
        color: #2d3748;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .section-description {
        color: #718096;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }

    .preference-item {
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .preference-item:hover {
        border-color: #667eea;
        background: #f7faff;
    }

    .preference-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .preference-info h6 {
        margin: 0 0 0.5rem 0;
        color: #2d3748;
        font-weight: 600;
    }

    .preference-info p {
        margin: 0;
        color: #718096;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .preference-control {
        flex-shrink: 0;
        margin-left: 1rem;
    }

    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .form-check-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }

    .account-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .info-item i {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .info-item h6 {
        margin: 0 0 0.25rem 0;
        color: #2d3748;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .info-item p {
        margin: 0;
        color: #4a5568;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .client-header-banner .row {
            text-align: center;
        }
        
        .client-sidebar {
            margin-bottom: 2rem;
        }

        .preference-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .preference-control {
            margin-left: 0;
            align-self: flex-end;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection