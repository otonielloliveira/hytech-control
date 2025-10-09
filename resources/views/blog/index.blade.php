@extends('layouts.blog')

@section('title', $config->meta_title ?? $config->site_name)
@section('description', $config->meta_description ?? $config->site_description)

@section('content')
    <!-- Banner Carousel -->
    @if($banners->count() > 0)
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
                                    <img src="https://via.placeholder.com/400x200?text=Blog+Post" 
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

    <!-- Últimas Postagens -->
    <section class="section" style="background: var(--light-bg);">
        <div class="container">
            <div class="section-title">
                <h2>📝 Últimas Postagens</h2>
                <p>Fique por dentro das nossas publicações mais recentes</p>
            </div>
            
            <div class="row">
                @forelse($latestPosts as $post)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <article class="post-card">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                     alt="{{ $post->title }}" loading="lazy">
                            @else
                                <img src="https://via.placeholder.com/400x200?text=Blog+Post" 
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
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">Nenhuma postagem encontrada.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Categorias -->
    @if($categories->count() > 0)
        <section class="section">
            <div class="container">
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
                                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                                 style="width: 80px; height: 80px; background-color: {{ $category->color }};">
                                                <i class="fas fa-tag text-white fa-2x"></i>
                                            </div>
                                        @endif
                                        
                                        <h5 class="card-title" style="color: {{ $category->color }};">
                                            {{ $category->name }}
                                        </h5>
                                        <p class="card-text text-muted small">
                                            {{ $category->description }}
                                        </p>
                                        <small class="text-muted">
                                            {{ $category->published_posts_count }} {{ Str::plural('post', $category->published_posts_count) }}
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
</style>
@endsection