@extends('layouts.blog')

@section('title', $config->meta_title ?? $config->site_name)
@section('description', $config->meta_description ?? $config->site_description)

@section('content')
    

    <!-- Seção de Produtos em Destaque -->
    @if($featuredProducts->count() > 0)
    <section class="store-featured-section py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container-fluid">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center bg-white bg-opacity-90 rounded-pill px-4 py-2 mb-3">
                    <i class="fas fa-shopping-bag text-primary me-2"></i>
                    <span class="fw-bold text-primary">Loja Oficial</span>
                </div>
                <h2 class="h3 text-white fw-bold mb-2">Produtos Exclusivos</h2>
                <p class="text-white-50 mb-0">Descubra nossa seleção especial de produtos únicos</p>
            </div>
            
            <div class="row g-3 mb-4">
                @foreach($featuredProducts as $product)
                <div class="col-lg-4 col-md-6">
                    <div class="card product-card h-100 border-0 shadow" style="border-radius: 12px; overflow: hidden; transform: translateY(0); transition: all 0.3s ease; max-width: 100%;">
                        <div class="position-relative">
                            <div class="product-image" style="height: 200px; overflow: hidden;">
                                @if($product->images && count($product->images) > 0)
                                    <img src="{{ Storage::url($product->images[0]) }}" 
                                         alt="{{ $product->name }}" 
                                         class="card-img-top w-100 h-100" 
                                         style="object-fit: cover; transition: transform 0.3s ease;">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                        <i class="fas fa-image text-muted" style="font-size: 2.5rem;"></i>
                                    </div>
                                @endif
                            </div>
                            
                            @if($product->isOnSale())
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                                        -{{ $product->getDiscountPercentage() }}%
                                    </span>
                                </div>
                            @endif
                            
                            @if(!$product->in_stock)
                                <div class="position-absolute top-0 start-0 end-0 bottom-0 d-flex align-items-center justify-content-center" 
                                     style="background: rgba(0,0,0,0.7); border-radius: 12px;">
                                    <span class="badge bg-dark px-3 py-2">Esgotado</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="card-body d-flex flex-column p-3">
                            <h6 class="card-title fw-bold text-dark mb-2" style="font-size: 1rem; line-height: 1.3;">{{ $product->name }}</h6>
                            
                            @if($product->short_description)
                                <p class="card-text text-muted small mb-2" style="line-height: 1.3; font-size: 0.85rem;">
                                    {{ Str::limit($product->short_description, 60) }}
                                </p>
                            @endif
                            
                            <div class="mt-auto">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    @if($product->isOnSale())
                                        <div>
                                            <span class="fw-bold text-danger mb-0" style="font-size: 1.1rem;">
                                                R$ {{ number_format($product->sale_price, 2, ',', '.') }}
                                            </span>
                                            <br>
                                            <small class="text-muted text-decoration-line-through" style="font-size: 0.8rem;">
                                                R$ {{ number_format($product->price, 2, ',', '.') }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="fw-bold text-primary mb-0" style="font-size: 1.1rem;">
                                            R$ {{ number_format($product->price, 2, ',', '.') }}
                                        </span>
                                    @endif
                                    
                                    @if($product->manage_stock && $product->stock_quantity <= 5 && $product->in_stock)
                                        <small class="text-warning fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Últimas {{ $product->stock_quantity }}
                                        </small>
                                    @endif
                                </div>
                                
                                <div class="d-grid gap-1">
                                    @if($product->in_stock)
                                        <button class="btn btn-primary btn-sm rounded-pill fw-semibold add-to-cart-homepage" 
                                                data-product-id="{{ $product->id }}"
                                                style="transition: all 0.3s ease; padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            <i class="fas fa-shopping-cart me-1"></i>
                                            Adicionar ao Carrinho
                                        </button>
                                        <a href="{{ route('store.product', $product->slug) }}" 
                                           class="btn btn-outline-primary btn-sm rounded-pill" style="padding: 0.3rem 0.8rem; font-size: 0.8rem;">
                                            <i class="fas fa-eye me-1"></i>
                                            Ver Detalhes
                                        </a>
                                    @else
                                        <button class="btn btn-secondary btn-sm rounded-pill" disabled style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            <i class="fas fa-times me-1"></i>
                                            Indisponível
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($featuredProducts->count() > 3)
            <div class="text-center">
                <a href="{{ route('store.index') }}" 
                   class="btn btn-light btn-lg rounded-pill px-5 py-3 fw-bold">
                    <i class="fas fa-store me-2"></i>
                    Ver Todos os Produtos
                </a>
            </div>
            @endif
        </div>
    </section>
    @endif

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
                    <section class="featured-section">
                        <div class="container-fluid">
                            <div class="section-header">
                                <div class="section-badge">
                                    <i class="fas fa-star"></i>
                                    Em Destaque
                                </div>
                                <h2 class="section-title">Conteúdos Principais</h2>
                                <p class="section-subtitle">Descubra nossos artigos mais importantes e relevantes</p>
                            </div>
                            
                            <div class="featured-grid">
                                @foreach($featuredPosts as $index => $post)
                                    <article class="featured-card">
                                        <div class="featured-image">
                                            @if($post->featured_image)
                                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                     alt="{{ $post->title }}" loading="lazy">
                                            @else
                                                <img src="{{ asset('images/default-no-image.png') }}" 
                                                     alt="{{ $post->title }}" loading="lazy">
                                            @endif
                                            
                                            @if($post->category)
                                                <span class="featured-category" style="background-color: {{ $post->category->color }}">
                                                    {{ $post->category->name }}
                                                </span>
                                            @endif
                                            
                                            @if($index === 0)
                                                <div class="featured-badge">
                                                    <i class="fas fa-crown"></i>
                                                    Principal
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="featured-content">
                                            <h3 class="featured-title">
                                                <a href="{{ route('blog.post.show', $post->slug) }}">
                                                    {{ $post->title }}
                                                </a>
                                            </h3>
                                            
                                            <p class="featured-excerpt">{{ $post->excerpt }}</p>
                                            
                                            <div class="featured-meta">
                                                <div class="author-info">
                                                    <i class="fas fa-user-circle"></i>
                                                    <span>{{ $post->user->name }}</span>
                                                </div>
                                                <div class="date-info">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <span>{{ $post->published_at->format('d/m/Y') }}</span>
                                                </div>
                                                @if($post->reading_time)
                                                    <div class="reading-time">
                                                        <i class="fas fa-clock"></i>
                                                        <span>{{ $post->reading_time }} min</span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <a href="{{ route('blog.post.show', $post->slug) }}" class="read-more-btn">
                                                Ler mais <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Artigos -->
                @if($artigosPosts->count() > 0)
                    <section class="articles-section">
                        <div class="container-fluid">
                            <div class="section-header">
                                <div class="section-badge articles">
                                    <i class="fas fa-newspaper"></i>
                                    Artigos
                                </div>
                                <h2 class="section-title">Análises e Reflexões</h2>
                                <p class="section-subtitle">Conteúdos aprofundados sobre diversos temas</p>
                            </div>
                            
                            <div class="articles-grid">
                                @foreach($artigosPosts as $post)
                                    <article class="article-card">
                                        <div class="article-image-wrapper">
                                            @if($post->featured_image)
                                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                     alt="{{ $post->title }}" loading="lazy" class="article-image">
                                            @else
                                                <img src="{{ asset('images/default-no-image.png') }}" 
                                                     alt="{{ $post->title }}" loading="lazy" class="article-image">
                                            @endif
                                            
                                            @if($post->category)
                                                <span class="article-category" style="background-color: {{ $post->category->color }}">
                                                    {{ $post->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="article-content">
                                            <h3 class="article-title">
                                                <a href="{{ route('blog.post.show', $post->slug) }}">
                                                    {{ $post->title }}
                                                </a>
                                            </h3>
                                            
                                            <p class="article-excerpt">{{ $post->excerpt }}</p>
                                            
                                            <div class="article-footer">
                                                <div class="article-meta">
                                                    <div class="meta-item">
                                                        <i class="fas fa-user"></i>
                                                        <span>{{ $post->user->name }}</span>
                                                    </div>
                                                    @if($post->reading_time)
                                                        <div class="meta-item">
                                                            <i class="fas fa-clock"></i>
                                                            <span>{{ $post->reading_time }} min</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <a href="{{ route('blog.post.show', $post->slug) }}" class="article-link">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
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
                    <section class="news-columns-section">
                        <div class="container-fluid">
                            <div class="section-header">
                                <div class="section-badge">
                                    <i class="fas fa-globe-americas"></i>
                                    Notícias
                                </div>
                                <h2 class="section-title">Panorama Geral</h2>
                                <p class="section-subtitle">Acompanhe as principais notícias do mundo, Brasil e região</p>
                            </div>
                            
                            <div class="row">
                                <!-- Notícias Mundiais -->
                                @if($noticiasMundiaisPosts->count() > 0)
                                    <div class="col-lg-4 mb-4">
                                        <div class="news-column">
                                            <h3 class="column-title">
                                                <i class="fas fa-globe"></i>
                                                Notícias Mundiais
                                            </h3>
                                            @foreach($noticiasMundiaisPosts as $post)
                                                <article class="news-item">
                                                    <h5><a href="{{ route('blog.post.show', $post->slug) }}">{{ $post->title }}</a></h5>
                                                    <p class="news-excerpt-small">{{ Str::limit($post->excerpt, 80) }}</p>
                                                    <div class="news-time">
                                                        <i class="fas fa-clock"></i>
                                                        <span>{{ $post->published_at->diffForHumans() }}</span>
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Notícias Nacionais -->
                                @if($noticiasNacionaisPosts->count() > 0)
                                    <div class="col-lg-4 mb-4">
                                        <div class="news-column">
                                            <h3 class="column-title">
                                                <i class="fas fa-flag"></i>
                                                Notícias Nacionais
                                            </h3>
                                            @foreach($noticiasNacionaisPosts as $post)
                                                <article class="news-item">
                                                    <h5><a href="{{ route('blog.post.show', $post->slug) }}">{{ $post->title }}</a></h5>
                                                    <p class="news-excerpt-small">{{ Str::limit($post->excerpt, 80) }}</p>
                                                    <div class="news-time">
                                                        <i class="fas fa-clock"></i>
                                                        <span>{{ $post->published_at->diffForHumans() }}</span>
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Notícias Regionais -->
                                @if($noticiasRegionaisPosts->count() > 0)
                                    <div class="col-lg-4 mb-4">
                                        <div class="news-column">
                                            <h3 class="column-title">
                                                <i class="fas fa-map-marker-alt"></i>
                                                Notícias Regionais
                                            </h3>
                                            @foreach($noticiasRegionaisPosts as $post)
                                                <article class="news-item">
                                                    <h5><a href="{{ route('blog.post.show', $post->slug) }}">{{ $post->title }}</a></h5>
                                                    <p class="news-excerpt-small">{{ Str::limit($post->excerpt, 80) }}</p>
                                                    <div class="news-time">
                                                        <i class="fas fa-clock"></i>
                                                        <span>{{ $post->published_at->diffForHumans() }}</span>
                                                    </div>
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
    /* Modern Section Headers */
    .section-header {
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
    }
    
    .section-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 1rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .section-badge.articles {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }
    
    .section-subtitle {
        font-size: 1.1rem;
        color: #6c757d;
        margin-bottom: 0;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Featured Section */
    .featured-section {
        padding: 4rem 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .featured-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .featured-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .featured-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    
    .featured-image {
        position: relative;
        overflow: hidden;
        height: 240px;
        flex-shrink: 0;
    }
    
    .featured-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }
    
    .featured-card:hover .featured-image img {
        transform: scale(1.05);
    }
    
    .featured-category {
        position: absolute;
        top: 1rem;
        left: 1rem;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
        z-index: 2;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .featured-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
        color: #333;
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        z-index: 2;
        box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
    }
    
    .featured-content {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .featured-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        line-height: 1.4;
        flex-grow: 1;
    }
    
    .featured-title a {
        color: #2d3748;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .featured-title a:hover {
        color: #667eea;
    }
    
    .featured-excerpt {
        color: #6c757d;
        margin-bottom: 1.5rem;
        line-height: 1.6;
        font-size: 0.95rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .featured-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .featured-meta > div {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    
    .featured-meta i {
        color: #667eea;
        font-size: 0.8rem;
    }
    
    .read-more-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        margin-top: auto;
        align-self: flex-start;
    }
    
    .read-more-btn:hover {
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    /* Articles Section */
    .articles-section {
        padding: 4rem 0;
        background: #fff;
    }
    
    .articles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        gap: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .article-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        border: 1px solid #f1f3f4;
    }
    
    .article-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        border-color: #667eea;
    }
    
    .article-image-wrapper {
        position: relative;
        height: 220px;
        overflow: hidden;
    }
    
    .article-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .article-card:hover .article-image {
        transform: scale(1.05);
    }
    
    .article-category {
        position: absolute;
        top: 1rem;
        left: 1rem;
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        z-index: 2;
    }
    
    .article-content {
        padding: 1.5rem;
    }
    
    .article-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        line-height: 1.4;
    }
    
    .article-title a {
        color: #2d3748;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .article-title a:hover {
        color: #28a745;
    }
    
    .article-excerpt {
        color: #6c757d;
        margin-bottom: 1.5rem;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .article-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .article-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    
    .meta-item i {
        color: #28a745;
    }
    
    .article-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 50%;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    
    .article-link:hover {
        color: white;
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .featured-grid {
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
    }
    
    @media (max-width: 768px) {
        .featured-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .featured-image {
            height: 200px !important;
        }
        
        .featured-content {
            padding: 1.2rem;
        }
        
        .featured-title {
            font-size: 1.1rem;
        }
        
        .featured-excerpt {
            font-size: 0.9rem;
            -webkit-line-clamp: 2;
        }
        
        .articles-grid {
            grid-template-columns: 1fr;
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .section-subtitle {
            font-size: 1rem;
        }
    }

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
    .news-columns-section {
        padding: 4rem 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }
    
    .news-column {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        height: 100%;
        border: 1px solid #f1f3f4;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .news-column::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    }
    
    .news-column:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        border-color: #667eea;
    }
    
    .column-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.4rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f3f4;
        position: relative;
    }
    
    .column-title i {
        font-size: 1.2rem;
        color: #667eea;
    }
    
    .column-title::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 50px;
        height: 2px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    }
    
    .news-item {
        padding: 1.5rem 0;
        border-bottom: 1px solid #f8f9fa;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .news-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .news-item:hover {
        background: #f8f9fa;
        margin: 0 -1rem;
        padding-left: 1rem;
        padding-right: 1rem;
        border-radius: 8px;
    }
    
    .news-item h5 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        line-height: 1.4;
        color: #2d3748;
    }
    
    .news-item a {
        color: #2d3748;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .news-item a:hover {
        color: #667eea;
    }
    
    .news-excerpt-small {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.75rem;
        line-height: 1.5;
    }
    
    .news-time {
        font-size: 0.8rem;
        color: #9ca3af;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    
    .news-time i {
        color: #667eea;
    }
    
    /* Seções Compactas (Política/Economia) */
    .compact-sections {
        padding: 4rem 0;
        background: white;
    }
    
    .politics-section,
    .economy-section {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid #f1f3f4;
        height: 100%;
    }
    
    .section-title-inline {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid #f1f3f4;
        position: relative;
    }
    
    .section-title-inline i {
        font-size: 1.3rem;
    }
    
    .politics-section .section-title-inline i {
        color: #dc3545;
    }
    
    .economy-section .section-title-inline i {
        color: #28a745;
    }
    
    .section-title-inline::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 60px;
        height: 3px;
    }
    
    .politics-section .section-title-inline::after {
        background: linear-gradient(90deg, #dc3545 0%, #e74c3c 100%);
    }
    
    .economy-section .section-title-inline::after {
        background: linear-gradient(90deg, #28a745 0%, #2ecc71 100%);
    }
    
    .compact-card {
        display: flex;
        gap: 1rem;
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.2rem;
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #e9ecef;
    }
    
    .compact-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        background: white;
        border-color: #667eea;
    }
    
    .compact-card img {
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
    }
    
    .compact-content {
        flex-grow: 1;
    }
    
    .compact-content h6 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        line-height: 1.3;
        color: #2d3748;
    }
    
    .compact-content a {
        color: #2d3748;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .compact-content a:hover {
        color: #667eea;
    }
    
    .compact-time {
        font-size: 0.8rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 0.5rem;
    }
    
    .compact-time i {
        color: #667eea;
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
    
    /* Estilos para seção de produtos */
    .store-featured-section {
        position: relative;
        overflow: hidden;
    }
    
    .store-featured-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000"/></svg>');
        background-size: cover;
    }
    
    .product-card {
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        max-height: 380px;
    }
    
    .product-card:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
    }
    
    .product-card:hover .product-image img {
        transform: scale(1.05);
    }
    
    .add-to-cart-homepage {
        position: relative;
        overflow: hidden;
        font-size: 0.85rem !important;
        padding: 0.4rem 0.8rem !important;
    }
    
    .add-to-cart-homepage:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    
    .add-to-cart-homepage.loading {
        pointer-events: none;
    }
    
    .add-to-cart-homepage.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid transparent;
        border-top: 2px solid #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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

    // Add to cart functionality for homepage
    $('.add-to-cart-homepage').on('click', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const productId = button.data('product-id');
        const originalText = button.html();
        
        // Disable button and show loading
        button.addClass('loading').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Carregando...');
        
        $.ajax({
            url: `/loja/carrinho/adicionar/${productId}`,
            type: 'POST',
            data: {
                quantity: 1
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Produto adicionado!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                    
                    // Update cart counter if exists
                    updateCartCounter(response.cart_totals.items_count);
                    
                    // Change button temporarily
                    button.removeClass('btn-primary').addClass('btn-success')
                          .html('<i class="fas fa-check me-1"></i>Adicionado!');
                    
                    setTimeout(() => {
                        button.removeClass('btn-success').addClass('btn-primary')
                              .html(originalText);
                    }, 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Erro ao adicionar produto ao carrinho';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: errorMessage
                });
            },
            complete: function() {
                button.removeClass('loading').prop('disabled', false);
                if (!button.hasClass('btn-success')) {
                    button.html(originalText);
                }
            }
        });
    });

    function updateCartCounter(count) {
        const counter = $('.cart-counter');
        if (counter.length) {
            counter.text(count);
            if (count > 0) {
                counter.removeClass('d-none').addClass('d-inline');
            } else {
                counter.removeClass('d-inline').addClass('d-none');
            }
        }
    }
</script>
@endsection