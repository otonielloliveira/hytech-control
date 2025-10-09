@extends('layouts.blog')

@section('title', $lecture->title . ' - Palestras - ' . $config->site_name)
@section('description', $lecture->description ?: 'Palestra com ' . $lecture->speaker)

@section('content')
@php
        $banners = App\Models\Banner::where('is_active', true)->orderBy('sort_order')->get();
    @endphp
    @if ($banners->count() > 0)
        <div class="blog-banner">
            <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach ($banners as $index => $banner)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                            class="{{ $index === 0 ? 'active' : '' }}"></button>
                    @endforeach
                </div>

                <div class="carousel-inner">
                    @foreach ($banners as $index => $banner)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}"
                            style="background-image: url('{{ $banner->image_url }}');">
                            <div class="carousel-overlay">
                                <div class="container">
                                    <div class="carousel-content">
                                        <h1>{{ $banner->title }}</h1>
                                        @if ($banner->subtitle)
                                            <h2>{{ $banner->subtitle }}</h2>
                                        @endif
                                        @if ($banner->description)
                                            <p>{{ $banner->description }}</p>
                                        @endif
                                        @if ($banner->link_url)
                                            <a href="{{ $banner->link_url }}" class="btn-hero"
                                                target="{{ $banner->target }}">
                                                {{ $banner->button_text }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($banners->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                @endif
            </div>
        </div>
    @endif

    <!-- Barra de Pesquisa e Login -->
    <section class="search-login-bar">
        <div class="container-fluid">
            <div class="row align-items-center py-3">
                <!-- Campo de Pesquisa -->
                <div class="col-lg-6 col-md-8 mb-2 mb-md-0">
                    <form action="{{ route('blog.search') }}" method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control search-input"
                                placeholder="🔍 Pesquisar posts, notícias, petições..." value="{{ request('q') }}"
                                autocomplete="off">
                            <button class="btn btn-search" type="submit">
                                <i class="fas fa-search"></i>
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Área de Login/Cadastro -->
                <div class="col-lg-6 col-md-4 text-end">
                    <div class="auth-buttons">
                        @auth('client')
                            <!-- Cliente logado -->
                            <div class="dropdown">
                                <a href="#" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i>
                                    Olá, {{ auth('client')->user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('client.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>Meu Painel
                                        </a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.profile') }}">
                                            <i class="fas fa-user-edit me-2"></i>Meu Perfil
                                        </a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.addresses') }}">
                                            <i class="fas fa-map-marker-alt me-2"></i>Endereços
                                        </a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.preferences') }}">
                                            <i class="fas fa-cog me-2"></i>Preferências
                                        </a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('client.logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <!-- Cliente não logado -->
                            <a href="#" class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal"
                                data-bs-target="#loginModal">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Entrar
                            </a>
                            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#registerModal">
                                <i class="fas fa-user-plus me-1"></i>
                                Cadastrar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>


<section class="section">
    <div class="container">
        <div class="row">
            <!-- Conteúdo Principal -->
            <div class="col-lg-8">
                <div class="lecture-content">
                    <!-- Informações da Palestra -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="mb-4">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Sobre a Palestra
                            </h3>
                            
                            @if($lecture->description)
                                <div class="lecture-description">
                                    {!! nl2br(e($lecture->description)) !!}
                                </div>
                            @endif

                            <!-- Detalhes do Evento -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <i class="fas fa-calendar text-primary me-2"></i>
                                        <strong>Data e Horário:</strong><br>
                                        {{ $lecture->date_time ? $lecture->date_time->format('d/m/Y às H:i') : 'Data a definir' }}
                                    </div>
                                </div>
                                @if($lecture->location)
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <strong>Local:</strong><br>
                                        {{ $lecture->location }}
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if($lecture->link_url)
                            <div class="text-center mt-4">
                                <a href="{{ $lecture->link_url }}" target="_blank" class="btn btn-success btn-lg">
                                    <i class="fas fa-external-link-alt me-2"></i>
                                    Participar da Palestra
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sobre o Palestrante -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="mb-4">
                                <i class="fas fa-user text-primary me-2"></i>
                                Palestrante
                            </h3>
                            
                            <div class="speaker-info">
                                <h4>{{ $lecture->speaker }}</h4>
                                <p class="text-muted">Palestrante Principal</p>
                                
                                <!-- Aqui você pode adicionar mais informações sobre o palestrante se tiver -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Ações Rápidas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            Ações
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($lecture->link_url)
                        <div class="d-grid gap-2 mb-3">
                            <a href="{{ $lecture->link_url }}" target="_blank" class="btn btn-success">
                                <i class="fas fa-external-link-alt me-2"></i>
                                Participar
                            </a>
                        </div>
                        @endif
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('lectures.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Voltar às Palestras
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Próximas Palestras -->
                @if($relatedLectures->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar me-2"></i>
                            Próximas Palestras
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($relatedLectures as $related)
                        <div class="related-lecture {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                            <h6 class="mb-1">
                                <a href="{{ route('lectures.show', $related) }}" class="text-decoration-none">
                                    {{ $related->title }}
                                </a>
                            </h6>
                            <small class="text-muted d-block">
                                <i class="fas fa-user me-1"></i>{{ $related->speaker }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-calendar me-1"></i>{{ $related->date_time ? $related->date_time->format('d/m/Y - H:i') : 'Data a definir' }}
                            </small>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<style>
.lecture-description {
    font-size: 1.1rem;
    line-height: 1.8;
    margin-bottom: 2rem;
}

.detail-item {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    border-left: 4px solid var(--primary-color);
}

.speaker-info h4 {
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.related-lecture h6 a {
    color: var(--text-color);
    transition: color 0.3s ease;
}

.related-lecture h6 a:hover {
    color: var(--primary-color);
}

.related-lecture small {
    margin-bottom: 2px;
}
</style>
@endsection