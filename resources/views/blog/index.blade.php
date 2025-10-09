@extends('layouts.blog')

@section('title', $config->meta_title ?? $config->site_name)
@section('description', $config->meta_description ?? $config->site_description)

@section('content')
    <!-- Banner Carousel - Fixo em todas as telas -->
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

    <!-- Main Content with Sidebar -->
    <div class="container-fluid mt-4">
        @php
            $sidebarConfig = App\Services\SidebarService::getSidebarConfig();
            $showSidebar = $sidebarConfig['show_sidebar'];
            $sidebarPosition = $sidebarConfig['position'] ?? 'right';
        @endphp
        
        <div class="row">
            @if($showSidebar && $sidebarPosition === 'left')
                <div class="col-lg-3">
                    @include('layouts.sidebar')
                </div>
            @endif
            
            <div class="@if($showSidebar) col-lg-9 @else col-lg-12 @endif">
                <!-- Posts em Destaque -->
                @if($featuredPosts->count() > 0)
                    <section class="section">
                        <div class="container-fluid">
                            <div class="section-title">
                                <h2>⭐ Posts em Destaque</h2>
                                <p>Confira nossos conteúdos mais importantes e relevantes</p>
                            </div>
                            
                            <div class="row">
                                @foreach($featuredPosts as $post)
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <article class="post-card">
                                            @if($post->featured_image)
                                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                     alt="{{ $post->title }}" loading="lazy">
                                            @else
                                                <img src="{{ asset('images/default-no-image.png') }}" 
                                                     alt="{{ $post->title }}" loading="lazy">
                                            @endif
                                            
                                            <div class="post-card-body">
                                                @if($post->category)
                                                    <span class="post-category" style="background-color: {{ $post->category->color }}">
                                                        {{ $post->category->name }}
                                                    </span>
                                                @endif
                                                
                                                <h3 class="post-title">
                                                    <a href="{{ route('blog.post.show', $post->slug) }}">
                                                        {{ $post->title }}
                                                    </a>
                                                </h3>
                                                
                                                <p class="post-excerpt">{{ $post->excerpt }}</p>
                                                
                                                <div class="post-meta">
                                                    <span>
                                                        <i class="fas fa-user me-1"></i>{{ $post->user->name }}
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-calendar me-1"></i>{{ $post->published_at->format('d/m/Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Artigos -->
                @if($artigosPosts->count() > 0)
                    <section class="section" style="background: var(--light-bg);">
                        <div class="container-fluid">
                            <div class="section-title">
                                <h2>📝 Artigos</h2>
                                <p>Conteúdos aprofundados e análises detalhadas</p>
                            </div>
                            
                            <div class="row">
                                @foreach($artigosPosts as $post)
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <article class="post-card">
                                            @if($post->featured_image)
                                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                     alt="{{ $post->title }}" loading="lazy">
                                            @else
                                                <img src="{{ asset('images/default-no-image.png') }}" 
                                                     alt="{{ $post->title }}" loading="lazy">
                                            @endif
                                            
                                            <div class="post-card-body">
                                                @if($post->category)
                                                    <span class="post-category" style="background-color: {{ $post->category->color }}">
                                                        {{ $post->category->name }}
                                                    </span>
                                                @endif
                                                
                                                <h3 class="post-title">
                                                    <a href="{{ route('blog.post.show', $post->slug) }}">
                                                        {{ $post->title }}
                                                    </a>
                                                </h3>
                                                
                                                <p class="post-excerpt">{{ $post->excerpt }}</p>
                                                
                                                <div class="post-meta">
                                                    <span>
                                                        <i class="fas fa-user me-1"></i>{{ $post->user->name }}
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-clock me-1"></i>{{ $post->reading_time }} min
                                                    </span>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Petições -->
                @if($peticoesPosts->count() > 0)
                    <section class="section">
                        <div class="container-fluid">
                            <div class="section-title">
                                <h2>✊ Petições</h2>
                                <p>Participe das nossas campanhas e faça a diferença</p>
                            </div>
                            
                            <div class="row">
                                @foreach($peticoesPosts as $post)
                                    <div class="col-lg-6 mb-4">
                                        <article class="petition-card">
                                            @if($post->featured_image)
                                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                     alt="{{ $post->title }}" loading="lazy" class="petition-image">
                                            @else
                                                <img src="{{ asset('images/default-no-image.png') }}" 
                                                     alt="{{ $post->title }}" loading="lazy" class="petition-image">
                                            @endif
                                            
                                            <div class="petition-card-body">
                                                @if($post->category)
                                                    <span class="petition-category" style="background-color: {{ $post->category->color }}">
                                                        {{ $post->category->name }}
                                                    </span>
                                                @endif
                                                
                                                <h3 class="petition-title">
                                                    <a href="{{ route('blog.post.show', $post->slug) }}">
                                                        {{ $post->title }}
                                                    </a>
                                                </h3>
                                                
                                                <p class="petition-excerpt">{{ $post->excerpt }}</p>
                                                
                                                <!-- Grupos WhatsApp resumido -->
                                                @if($post->whatsapp_groups && count($post->whatsapp_groups) > 0)
                                                    <div class="whatsapp-preview mb-3">
                                                        <small class="text-success">
                                                            <i class="fab fa-whatsapp me-1"></i>
                                                            {{ count(array_filter($post->whatsapp_groups, fn($group) => $group['status'] === 'ativo')) }} grupos ativos disponíveis
                                                        </small>
                                                    </div>
                                                @endif
                                                
                                                <div class="petition-action">
                                                    <a href="{{ route('blog.post.show', $post->slug) }}" 
                                                       class="btn btn-danger btn-sm">
                                                        <i class="fas fa-hand-fist me-1"></i>Apoiar Petição
                                                    </a>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Últimas Notícias -->
                @if($ultimasNoticiasPosts->count() > 0)
                    <section class="section" style="background: var(--light-bg);">
                        <div class="container-fluid">
                            <div class="section-title">
                                <h2>📰 Últimas Notícias</h2>
                                <p>Fique por dentro dos acontecimentos mais recentes</p>
                            </div>
                            
                            <div class="row">
                                @foreach($ultimasNoticiasPosts as $post)
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <article class="news-card">
                                            @if($post->featured_image)
                                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                     alt="{{ $post->title }}" loading="lazy">
                                            @else
                                                <img src="{{ asset('images/default-no-image.png') }}" 
                                                     alt="{{ $post->title }}" loading="lazy">
                                            @endif
                                            
                                            <div class="news-card-body">
                                                <span class="breaking-badge">ÚLTIMAS</span>
                                                @if($post->category)
                                                    <span class="news-category" style="background-color: {{ $post->category->color }}">
                                                        {{ $post->category->name }}
                                                    </span>
                                                @endif
                                                
                                                <h3 class="news-title">
                                                    <a href="{{ route('blog.post.show', $post->slug) }}">
                                                        {{ $post->title }}
                                                    </a>
                                                </h3>
                                                
                                                <p class="news-excerpt">{{ $post->excerpt }}</p>
                                                
                                                <div class="news-meta">
                                                    <span><i class="fas fa-clock me-1"></i>{{ $post->published_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Seção de Notícias em Colunas -->
                @if($noticiasMundiaisPosts->count() > 0 || $noticiasNacionaisPosts->count() > 0 || $noticiasRegionaisPosts->count() > 0)
                    <section class="section">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- Notícias Mundiais -->
                                @if($noticiasMundiaisPosts->count() > 0)
                                    <div class="col-lg-4 mb-4">
                                        <div class="news-column">
                                            <h3 class="column-title">🌍 Notícias Mundiais</h3>
                                            @foreach($noticiasMundiaisPosts as $post)
                                                <article class="news-item">
                                                    <h5><a href="{{ route('blog.post.show', $post->slug) }}">{{ $post->title }}</a></h5>
                                                    <p class="news-excerpt-small">{{ Str::limit($post->excerpt, 80) }}</p>
                                                    <small class="text-muted">{{ $post->published_at->diffForHumans() }}</small>
                                                </article>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Notícias Nacionais -->
                                @if($noticiasNacionaisPosts->count() > 0)
                                    <div class="col-lg-4 mb-4">
                                        <div class="news-column">
                                            <h3 class="column-title">🇧🇷 Notícias Nacionais</h3>
                                            @foreach($noticiasNacionaisPosts as $post)
                                                <article class="news-item">
                                                    <h5><a href="{{ route('blog.post.show', $post->slug) }}">{{ $post->title }}</a></h5>
                                                    <p class="news-excerpt-small">{{ Str::limit($post->excerpt, 80) }}</p>
                                                    <small class="text-muted">{{ $post->published_at->diffForHumans() }}</small>
                                                </article>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Notícias Regionais -->
                                @if($noticiasRegionaisPosts->count() > 0)
                                    <div class="col-lg-4 mb-4">
                                        <div class="news-column">
                                            <h3 class="column-title">🏙️ Notícias Regionais</h3>
                                            @foreach($noticiasRegionaisPosts as $post)
                                                <article class="news-item">
                                                    <h5><a href="{{ route('blog.post.show', $post->slug) }}">{{ $post->title }}</a></h5>
                                                    <p class="news-excerpt-small">{{ Str::limit($post->excerpt, 80) }}</p>
                                                    <small class="text-muted">{{ $post->published_at->diffForHumans() }}</small>
                                                </article>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Política e Economia em Linha -->
                @if($politicaPosts->count() > 0 || $economiaPosts->count() > 0)
                    <section class="section" style="background: var(--light-bg);">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- Política -->
                                @if($politicaPosts->count() > 0)
                                    <div class="col-lg-6 mb-4">
                                        <div class="politics-section">
                                            <h3 class="section-title-inline">🏛️ Política</h3>
                                            <div class="row">
                                                @foreach($politicaPosts->take(4) as $post)
                                                    <div class="col-md-6 mb-3">
                                                        <article class="compact-card">
                                                            @if($post->featured_image)
                                                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                                     alt="{{ $post->title }}" loading="lazy">
                                                            @else
                                                                <img src="{{ asset('images/default-no-image.png') }}" 
                                                                     alt="{{ $post->title }}" loading="lazy">
                                                            @endif
                                                            <div class="compact-content">
                                                                <h6><a href="{{ route('blog.post.show', $post->slug) }}">{{ $post->title }}</a></h6>
                                                                <small class="text-muted">{{ $post->published_at->diffForHumans() }}</small>
                                                            </div>
                                                        </article>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Economia -->
                                @if($economiaPosts->count() > 0)
                                    <div class="col-lg-6 mb-4">
                                        <div class="economy-section">
                                            <h3 class="section-title-inline">💰 Economia</h3>
                                            <div class="row">
                                                @foreach($economiaPosts->take(4) as $post)
                                                    <div class="col-md-6 mb-3">
                                                        <article class="compact-card">
                                                            @if($post->featured_image)
                                                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                                     alt="{{ $post->title }}" loading="lazy">
                                                            @else
                                                                <img src="{{ asset('images/default-no-image.png') }}" 
                                                                     alt="{{ $post->title }}" loading="lazy">
                                                            @endif
                                                            <div class="compact-content">
                                                                <h6><a href="{{ route('blog.post.show', $post->slug) }}">{{ $post->title }}</a></h6>
                                                                <small class="text-muted">{{ $post->published_at->diffForHumans() }}</small>
                                                            </div>
                                                        </article>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Fallback: Últimas Postagens (se não houver posts suficientes por destino) -->
                @if($latestPosts->count() > 0 && ($artigosPosts->count() === 0 && $ultimasNoticiasPosts->count() === 0))
                    <section class="section">
                        <div class="container-fluid">
                            <div class="section-title">
                                <h2>📝 Últimas Postagens</h2>
                                <p>Fique por dentro das nossas publicações mais recentes</p>
                            </div>
                            
                            <div class="row">
                                @foreach($latestPosts as $post)
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <article class="post-card">
                                            @if($post->featured_image)
                                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                     alt="{{ $post->title }}" loading="lazy">
                                            @else
                                                <img src="{{ asset('images/default-no-image.png') }}" 
                                                     alt="{{ $post->title }}" loading="lazy">
                                            @endif
                                            
                                            <div class="post-card-body">
                                                @if($post->category)
                                                    <span class="post-category" style="background-color: {{ $post->category->color }}">
                                                        {{ $post->category->name }}
                                                    </span>
                                                @endif
                                                
                                                <h3 class="post-title">
                                                    <a href="{{ route('blog.post.show', $post->slug) }}">
                                                        {{ $post->title }}
                                                    </a>
                                                </h3>
                                                
                                                <p class="post-excerpt">{{ $post->excerpt }}</p>
                                                
                                                <div class="post-meta">
                                                    <span>
                                                        <i class="fas fa-user me-1"></i>{{ $post->user->name }}
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-clock me-1"></i>{{ $post->reading_time }} min
                                                    </span>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Amigos e Apoiadores -->
                @if($amigosApoiadoresPosts->count() > 0)
                    <section class="section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="container-fluid">
                            <div class="section-title text-center text-white">
                                <h2>🤝 Amigos e Apoiadores</h2>
                                <p>Conheça nossos parceiros e apoiadores</p>
                            </div>
                            
                            <div id="amigosCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                                <div class="carousel-inner">
                                    @foreach($amigosApoiadoresPosts->chunk(4) as $chunkIndex => $chunk)
                                        <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                                            <div class="row">
                                                @foreach($chunk as $post)
                                                    <div class="col-lg-3 col-md-6 mb-4">
                                                        <article class="amigos-card">
                                                            @if($post->featured_image)
                                                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                                     alt="{{ $post->title }}" loading="lazy">
                                                            @else
                                                                <img src="{{ asset('images/default-no-image.png') }}" 
                                                                     alt="{{ $post->title }}" loading="lazy">
                                                            @endif
                                                            <div class="amigos-content">
                                                                <h5><a href="{{ route('blog.post.show', $post->slug) }}">{{ $post->title }}</a></h5>
                                                                <p>{{ Str::limit(strip_tags($post->excerpt ?: $post->content), 100) }}</p>
                                                                <small class="text-muted">{{ $post->published_at->diffForHumans() }}</small>
                                                            </div>
                                                        </article>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if($amigosApoiadoresPosts->count() > 4)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#amigosCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                        <span class="visually-hidden">Anterior</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#amigosCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                        <span class="visually-hidden">Próximo</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Categorias -->
                @if($categories->count() > 0)
                    <section class="section">
                        <div class="container-fluid">
                            <div class="section-title">
                                <h2>🏷️ Explore por Categoria</h2>
                                <p>Navegue pelos nossos conteúdos organizados por tema</p>
                            </div>
                            
                            <div class="row">
                                @foreach($categories as $category)
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                        <a href="{{ route('blog.category.show', $category->slug) }}" 
                                           class="text-decoration-none">
                                            <div class="card h-100 border-0 shadow-sm category-card">
                                                <div class="card-body text-center">
                                                    @if($category->image)
                                                        <img src="{{ asset('storage/' . $category->image) }}" 
                                                             alt="{{ $category->name }}" 
                                                             class="rounded-circle mb-3" 
                                                             style="width: 80px; height: 80px; object-fit: cover;">
                                                    @else
                                                        <img src="{{ asset('images/default-no-image.png') }}" 
                                                             alt="{{ $category->name }}" 
                                                             class="rounded-circle mb-3" 
                                                             style="width: 80px; height: 80px; object-fit: cover;">
                                                    @endif
                                                    
                                                    <h5 class="card-title" style="color: {{ $category->color }};">
                                                        {{ $category->name }}
                                                    </h5>
                                                    <p class="card-text text-muted small">
                                                        {{ $category->description }}
                                                    </p>
                                                    <small class="text-muted">
                                                        {{ $category->posts_count }} {{ Str::plural('post', $category->posts_count) }}
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            </div>
            
            @if($showSidebar && $sidebarPosition === 'right')
                <div class="col-lg-3">
                    @include('layouts.sidebar')
                </div>
            @endif
        </div>
    </div>
    
    <!-- Modal de Login -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="loginModalLabel">
                        <i class="fas fa-sign-in-alt me-2 text-primary"></i>
                        Entrar na sua conta
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <form id="loginForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="loginEmail" name="email" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="loginPassword" name="password" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                            <label class="form-check-label" for="rememberMe">
                                Lembrar de mim
                            </label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Entrar
                            </button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <p class="mb-0">
                            Não tem uma conta? 
                            <a href="#" onclick="switchToRegister()" class="text-primary text-decoration-none">
                                Cadastre-se aqui
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Cadastro -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="registerModalLabel">
                        <i class="fas fa-user-plus me-2 text-success"></i>
                        Criar nova conta
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <form id="registerForm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="registerName" class="form-label">Nome completo *</label>
                                <input type="text" class="form-control" id="registerName" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="registerEmail" class="form-label">E-mail *</label>
                                <input type="email" class="form-control" id="registerEmail" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="registerPhone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="registerPhone" name="phone" placeholder="(11) 99999-9999">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="registerPassword" class="form-label">Senha *</label>
                                <input type="password" class="form-control" id="registerPassword" name="password" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="registerPasswordConfirmation" class="form-label">Confirmar senha *</label>
                                <input type="password" class="form-control" id="registerPasswordConfirmation" name="password_confirmation" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="registerBirthDate" class="form-label">Data de nascimento</label>
                                <input type="date" class="form-control" id="registerBirthDate" name="birth_date">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="registerGender" class="form-label">Gênero</label>
                                <select class="form-select" id="registerGender" name="gender">
                                    <option value="">Selecione</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="feminino">Feminino</option>
                                    <option value="outro">Outro</option>
                                    <option value="nao_informar">Prefiro não informar</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i>
                                Criar conta
                            </button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <p class="mb-0">
                            Já tem uma conta? 
                            <a href="#" onclick="switchToLogin()" class="text-primary text-decoration-none">
                                Faça login aqui
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
    .category-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    /* Estilos para Petições */
    .petition-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    
    .petition-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }
    
    .petition-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    .petition-card-body {
        padding: 1.5rem;
    }
    
    .petition-category {
        display: inline-block;
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .petition-title {
        font-size: 1.2rem;
        margin-bottom: 1rem;
        line-height: 1.3;
    }
    
    .petition-title a {
        color: #2d3748;
        text-decoration: none;
    }
    
    .petition-title a:hover {
        color: #dc3545;
    }
    
    .petition-excerpt {
        color: #6c757d;
        margin-bottom: 1rem;
        line-height: 1.6;
    }
    
    .whatsapp-preview {
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 8px;
        border-left: 3px solid #25d366;
    }
    
    /* Estilos para Notícias */
    .news-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    
    .news-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    
    .news-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }
    
    .news-card-body {
        padding: 1.2rem;
    }
    
    .breaking-badge {
        background: #dc3545;
        color: white;
        padding: 0.2rem 0.6rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: bold;
        margin-right: 0.5rem;
        animation: pulse 2s infinite;
    }
    
    .news-category {
        display: inline-block;
        color: white;
        padding: 0.3rem 0.6rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-bottom: 0.8rem;
    }
    
    .news-title {
        font-size: 1rem;
        margin-bottom: 0.8rem;
        line-height: 1.4;
    }
    
    .news-title a {
        color: #2d3748;
        text-decoration: none;
    }
    
    .news-title a:hover {
        color: #0984e3;
    }
    
    .news-excerpt {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .news-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    /* Colunas de Notícias */
    .news-column {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        height: 100%;
    }
    
    .column-title {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.8rem;
        margin-bottom: 1.5rem;
        font-size: 1.2rem;
    }
    
    .news-item {
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #f1f3f4;
    }
    
    .news-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .news-item h5 {
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }
    
    .news-item a {
        color: #2d3748;
        text-decoration: none;
    }
    
    .news-item a:hover {
        color: #0984e3;
    }
    
    .news-excerpt-small {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }
    
    /* Seções Compactas (Política/Economia) */
    .section-title-inline {
        border-bottom: 3px solid #e9ecef;
        padding-bottom: 0.8rem;
        margin-bottom: 1.5rem;
        font-size: 1.3rem;
    }
    
    .compact-card {
        display: flex;
        gap: 1rem;
        background: white;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        height: 100%;
    }
    
    .compact-card:hover {
        transform: translateY(-2px);
    }
    
    .compact-card img {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        flex-shrink: 0;
    }
    
    .compact-content h6 {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
        line-height: 1.3;
    }
    
    .compact-content a {
        color: #2d3748;
        text-decoration: none;
    }
    
    .compact-content a:hover {
        color: #0984e3;
    }
    
    /* Amigos e Apoiadores Carousel */
    .amigos-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .amigos-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .amigos-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 1rem;
    }
    
    .amigos-content h5 {
        font-size: 1.1rem;
        margin-bottom: 0.8rem;
        color: #2d3748;
    }
    
    .amigos-content h5 a {
        color: #2d3748;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .amigos-content h5 a:hover {
        color: #667eea;
    }
    
    .amigos-content p {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 0.8rem;
        line-height: 1.4;
    }
    
    #amigosCarousel .carousel-control-prev,
    #amigosCarousel .carousel-control-next {
        width: 5%;
        color: white;
    }
    
    #amigosCarousel .carousel-control-prev-icon,
    #amigosCarousel .carousel-control-next-icon {
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        width: 40px;
        height: 40px;
    }

    /* Search and Login Bar */
    .search-login-bar {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .search-form .input-group {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-radius: 25px;
        overflow: hidden;
    }

    .search-input {
        border: none;
        padding: 12px 20px;
        font-size: 1rem;
        background: white;
    }

    .search-input:focus {
        box-shadow: none;
        border-color: transparent;
    }

    .btn-search {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 12px 25px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-search:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        color: white;
        transform: translateY(-1px);
    }

    .auth-buttons .btn {
        border-radius: 20px;
        padding: 8px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .auth-buttons .btn-outline-primary {
        border-color: #667eea;
        color: #667eea;
    }

    .auth-buttons .btn-outline-primary:hover {
        background: #667eea;
        border-color: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    .auth-buttons .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .auth-buttons .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .search-login-bar .col-lg-6 {
            text-align: center !important;
        }
        
        .auth-buttons {
            justify-content: center;
            display: flex;
        }
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
</style>
@endsection

@section('scripts')
<script>
    // Switch between login and register modals
    function switchToRegister() {
        $('#loginModal').modal('hide');
        $('#registerModal').modal('show');
    }

    function switchToLogin() {
        $('#registerModal').modal('hide');
        $('#loginModal').modal('show');
    }

    // Handle login form submission
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Clear previous errors
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');
        
        // Disable submit button
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Entrando...');
        
        $.ajax({
            url: '{{ route("client.auth.login") }}',
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Login realizado!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        const input = form.find(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[field][0]);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Ocorreu um erro. Tente novamente.'
                    });
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Handle register form submission
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Clear previous errors
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');
        
        // Disable submit button
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Criando conta...');
        
        $.ajax({
            url: '{{ route("client.auth.register") }}',
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Conta criada!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        const input = form.find(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[field][0]);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Ocorreu um erro. Tente novamente.'
                    });
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Phone mask
    $('#registerPhone').on('input', function() {
        let value = this.value.replace(/\D/g, '');
        value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
        value = value.replace(/(\d)(\d{4})$/, '$1-$2');
        this.value = value;
    });
</script>
@endsection