@extends('layouts.blog')

@section('title', "Resultados da pesquisa: {$query}")
@section('description', "Resultados da pesquisa por '{$query}' no blog")

@section('content')
    <!-- Banner Carousel - Fixo em todas as telas -->
    @php
        $banners = App\Models\Banner::where('is_active', true)->orderBy('sort_order')->get();
    @endphp
    @if($banners->count() > 0)
        <div class="blog-banner">
            <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach($banners as $index => $banner)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}"></button>
                    @endforeach
                </div>
                
                <div class="carousel-inner">
                    @foreach($banners as $index => $banner)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" 
                             style="background-image: url('{{ $banner->image_url }}');">
                            <div class="carousel-overlay">
                                <div class="container">
                                    <div class="carousel-content">
                                        <h1>{{ $banner->title }}</h1>
                                        @if($banner->subtitle)
                                            <h2>{{ $banner->subtitle }}</h2>
                                        @endif
                                        @if($banner->description)
                                            <p>{{ $banner->description }}</p>
                                        @endif
                                        @if($banner->link_url)
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
                
                @if($banners->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
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
                            <input type="text" 
                                   name="q" 
                                   class="form-control search-input" 
                                   placeholder="🔍 Pesquisar posts, notícias, petições..." 
                                   value="{{ request('q') }}"
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
                                <a href="#" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
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
                                    <li><hr class="dropdown-divider"></li>
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
                            <a href="#" class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Entrar
                            </a>
                            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#registerModal">
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
            <div class="col-12">
                <!-- Cabeçalho da pesquisa -->
                <div class="search-header mb-4">
                    <h1 class="h2">Resultados da pesquisa</h1>
                    <p class="text-muted">
                        <strong>{{ $resultsCount }}</strong> 
                        {{ $resultsCount === 1 ? 'resultado encontrado' : 'resultados encontrados' }} 
                        para "<strong>{{ $query }}</strong>"
                    </p>
                </div>

                <!-- Nova pesquisa -->
                <div class="search-again mb-4">
                    <form action="{{ route('blog.search') }}" method="GET" class="row g-2">
                        <div class="col-md-8">
                            <input type="text" 
                                   name="q" 
                                   class="form-control" 
                                   placeholder="Refinar pesquisa..." 
                                   value="{{ $query }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>
                                Pesquisar
                            </button>
                        </div>
                    </form>
                </div>

                @if($posts->count() > 0)
                    <!-- Resultados -->
                    <div class="search-results">
                        <div class="row">
                            @foreach($posts as $post)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <article class="search-result-card">
                                        @if($post->featured_image)
                                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                 alt="{{ $post->title }}" 
                                                 class="result-image"
                                                 loading="lazy">
                                        @else
                                            <div class="result-placeholder">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="result-content">
                                            @if($post->category)
                                                <span class="result-category" style="background-color: {{ $post->category->color }};">
                                                    {{ $post->category->name }}
                                                </span>
                                            @endif
                                            
                                            <h3 class="result-title">
                                                <a href="{{ route('blog.post.show', $post->slug) }}">
                                                    {{ $post->title }}
                                                </a>
                                            </h3>
                                            
                                            @if($post->excerpt)
                                                <p class="result-excerpt">{{ $post->excerpt }}</p>
                                            @endif
                                            
                                            <div class="result-meta">
                                                <span class="meta-item">
                                                    <i class="fas fa-user"></i>
                                                    {{ $post->user->name }}
                                                </span>
                                                <span class="meta-item">
                                                    <i class="fas fa-calendar"></i>
                                                    {{ $post->published_at->format('d/m/Y') }}
                                                </span>
                                                <span class="meta-item">
                                                    <i class="fas fa-eye"></i>
                                                    {{ $post->views_count }} visualizações
                                                </span>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginação -->
                        @if($posts->hasPages())
                            <div class="pagination-wrapper mt-4">
                                {{ $posts->appends(['q' => $query])->links() }}
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Nenhum resultado -->
                    <div class="no-results text-center py-5">
                        <div class="no-results-icon mb-3">
                            <i class="fas fa-search" style="font-size: 4rem; color: #ccc;"></i>
                        </div>
                        <h3>Nenhum resultado encontrado</h3>
                        <p class="text-muted mb-4">
                            Não encontramos nenhum post que corresponda à sua pesquisa por "<strong>{{ $query }}</strong>".
                        </p>
                        <div class="suggestions">
                            <h5>Sugestões:</h5>
                            <ul class="list-unstyled">
                                <li>• Verifique a ortografia das palavras</li>
                                <li>• Tente usar termos mais gerais</li>
                                <li>• Use palavras-chave diferentes</li>
                                <li>• Explore nossas categorias de conteúdo</li>
                            </ul>
                        </div>
                        <a href="{{ route('blog.index') }}" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i>
                            Voltar ao início
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .search-header h1 {
        color: #2d3748;
        font-weight: 600;
    }

    .search-again {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        border-left: 4px solid #0984e3;
    }

    .search-result-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        overflow: hidden;
    }

    .search-result-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .result-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .result-placeholder {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
    }

    .result-content {
        padding: 1.5rem;
    }

    .result-category {
        display: inline-block;
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 500;
        margin-bottom: 0.8rem;
    }

    .result-title {
        font-size: 1.2rem;
        margin-bottom: 0.8rem;
        line-height: 1.3;
    }

    .result-title a {
        color: #2d3748;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .result-title a:hover {
        color: #0984e3;
    }

    .result-excerpt {
        color: #666;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .result-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.8rem;
        color: #888;
    }

    .meta-item i {
        margin-right: 0.3rem;
    }

    .no-results {
        max-width: 500px;
        margin: 0 auto;
    }

    .suggestions {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 2rem;
    }

    .suggestions h5 {
        color: #2d3748;
        margin-bottom: 1rem;
    }

    .suggestions ul li {
        margin-bottom: 0.5rem;
        color: #666;
    }

    @media (max-width: 768px) {
        .result-meta {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
@endsection