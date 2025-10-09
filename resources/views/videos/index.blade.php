@extends('layouts.blog')

@section('title', 'Vídeos')

@section('content')
    <!-- Banner Carousel -->
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


<div class="container-fluid mt-4">
    <div class="row">
        <!-- Conteúdo Principal -->
        <div class="col-lg-12">
            <h1 class="text-center mb-4">Vídeos</h1>
            <p class="text-center text-muted mb-5">Assista aos nossos vídeos e conteúdos em vídeo</p>
            
            <!-- Search and Filters -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Buscar vídeos..."
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                @if($categories && count($categories) > 0)
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="categoria" class="form-select" onchange="this.form.submit()">
                            <option value="">Todas as categorias</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" 
                                        {{ request('categoria') === $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @endif
            </div>
            
            <!-- Active Filters -->
            @if(request('search') || request('categoria'))
                <div class="mb-4">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted">Filtros ativos:</span>
                        
                        @if(request('search'))
                            <span class="badge bg-primary">
                                Busca: "{{ request('search') }}"
                                <a href="{{ request()->url() }}?categoria={{ request('categoria') }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                        
                        @if(request('categoria'))
                            <span class="badge bg-secondary">
                                Categoria: {{ ucfirst(request('categoria')) }}
                                <a href="{{ request()->url() }}?search={{ request('search') }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
            
            @if($videos->count() > 0)
                <div class="row g-4">
                    @foreach($videos as $video)
                        <div class="col-lg-6 col-md-6">
                            <div class="card h-100 shadow-sm video-card">
                                <div class="position-relative video-thumbnail">
                                    @if($video->thumbnail_url)
                                        <img src="{{ $video->thumbnail_url }}" 
                                             class="card-img-top" 
                                             alt="{{ $video->title }}"
                                             style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="fas fa-video fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Play Button Overlay -->
                                    <div class="video-play-overlay">
                                        <div class="play-button">
                                            <i class="fas fa-play"></i>
                                        </div>
                                    </div>
                                    
                                    @if($video->duration)
                                        <div class="video-duration">
                                            {{ $video->formatted_duration }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $video->title }}</h5>
                                    
                                    @if($video->description)
                                        <p class="card-text text-muted flex-grow-1">
                                            {{ Str::limit($video->description, 100) }}
                                        </p>
                                    @endif
                                    
                                    <div class="video-meta mb-3">
                                        @if($video->category)
                                            <span class="badge bg-primary me-2">{{ ucfirst($video->category) }}</span>
                                        @endif
                                        
                                        @if($video->views_count)
                                            <small class="text-muted">
                                                <i class="fas fa-eye me-1"></i>
                                                {{ number_format($video->views_count) }} visualizações
                                            </small>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <a href="{{ route('videos.show', $video) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-play me-1"></i>
                                            Assistir Vídeo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Paginação -->
                @if($videos->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $videos->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-video fa-4x text-muted mb-3"></i>
                    <h3 class="text-muted">Nenhum vídeo encontrado</h3>
                    @if(request('search') || request('categoria'))
                        <p class="text-muted">Tente ajustar os filtros de busca.</p>
                        <a href="{{ route('videos.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-refresh me-1"></i>
                            Limpar Filtros
                        </a>
                    @else
                        <p class="text-muted">Ainda não há vídeos disponíveis.</p>
                    @endif
                </div>
            @endif
        </div>
        
       
    </div>
</div>

<style>
.video-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.video-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.video-thumbnail {
    position: relative;
    overflow: hidden;
}

.video-play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.video-card:hover .video-play-overlay {
    opacity: 1;
}

.play-button {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #007bff;
    font-size: 1.5rem;
    transition: all 0.3s ease;
}

.play-button:hover {
    background: white;
    transform: scale(1.1);
}

.video-duration {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.8rem;
}

.sidebar {
    padding-top: 2rem;
}

.sidebar-widget {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
}

.widget-header {
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f8f9fa;
}

.widget-header h5 {
    margin: 0;
    color: #495057;
    font-weight: 600;
}

.widget-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.widget-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.widget-icon {
    color: #6c757d;
    width: 20px;
    text-align: center;
}

.widget-info h6 a {
    color: #495057;
    text-decoration: none;
    font-size: 0.9rem;
    line-height: 1.3;
}

.widget-info h6 a:hover {
    color: #007bff;
}

.tag-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag-link {
    display: inline-block;
    padding: 0.3rem 0.6rem;
    background: #f8f9fa;
    color: #6c757d;
    text-decoration: none;
    border-radius: 15px;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.tag-link:hover {
    background: #007bff;
    color: white;
}

.tag-count {
    font-size: 0.7rem;
    opacity: 0.7;
}
</style>
@endsection