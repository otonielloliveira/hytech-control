@extends('layouts.blog')

@section('title', $title . ' - ' . $config->site_name)
@section('description', $description)

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
        </div>
    @endif

    <!-- Page Header -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header text-center mb-5">
                    <h1 class="display-4 fw-bold text-primary">🌍 {{ $title }}</h1>
                    <p class="lead text-muted">{{ $description }}</p>
                    <div class="breaking-news-badge">
                        <span class="badge bg-danger fs-6 pulse">🔴 AO VIVO</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content with Sidebar -->
    <div class="container-fluid">
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
                <!-- Latest News Grid -->
                <section class="section">
                    <div class="container-fluid">
                        @if($posts->count() > 0)
                            <!-- Destaque Principal -->
                            <div class="row mb-5">
                                <div class="col-lg-8">
                                    @php $featuredPost = $posts->first(); @endphp
                                    <article class="featured-news-card">
                                        @if($featuredPost->featured_image)
                                            <img src="{{ asset('storage/' . $featuredPost->featured_image) }}" 
                                                 alt="{{ $featuredPost->title }}" loading="lazy" class="featured-image">
                                        @else
                                            <img src="{{ asset('images/default-no-image.png') }}" 
                                                 alt="{{ $featuredPost->title }}" loading="lazy" class="featured-image">
                                        @endif
                                        
                                        <div class="featured-content">
                                            <span class="breaking-badge">DESTAQUE</span>
                                            @if($featuredPost->category)
                                                <span class="news-category" style="background-color: {{ $featuredPost->category->color }}">
                                                    {{ $featuredPost->category->name }}
                                                </span>
                                            @endif
                                            
                                            <h2 class="featured-title">
                                                <a href="{{ route('blog.post.show', $featuredPost->slug) }}">
                                                    {{ $featuredPost->title }}
                                                </a>
                                            </h2>
                                            
                                            <p class="featured-excerpt">{{ $featuredPost->excerpt }}</p>
                                            
                                            <div class="news-meta">
                                                <span><i class="fas fa-user me-1"></i>{{ $featuredPost->user->name }}</span>
                                                <span><i class="fas fa-clock me-1"></i>{{ $featuredPost->published_at->diffForHumans() }}</span>
                                                <span><i class="fas fa-eye me-1"></i>{{ $featuredPost->views_count ?? 0 }} visualizações</span>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                                
                                <div class="col-lg-4">
                                    <div class="latest-sidebar">
                                        <h4 class="sidebar-title">Mais Recentes</h4>
                                        @foreach($posts->skip(1)->take(4) as $post)
                                            <article class="sidebar-news-item">
                                                @if($post->featured_image)
                                                    <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                         alt="{{ $post->title }}" loading="lazy">
                                                @else
                                                    <img src="{{ asset('images/default-no-image.png') }}" 
                                                         alt="{{ $post->title }}" loading="lazy">
                                                @endif
                                                
                                                <div class="sidebar-content">
                                                    <h6><a href="{{ route('blog.post.show', $post->slug) }}">{{ $post->title }}</a></h6>
                                                    <small class="text-muted">{{ $post->published_at->diffForHumans() }}</small>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Grid de Notícias -->
                        <div class="row">
                            @foreach($posts->skip(5) as $post)
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
                                                <span><i class="fas fa-eye me-1"></i>{{ $post->views_count ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($posts->count() === 0)
                            <div class="text-center py-5">
                                <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                <h3>Nenhuma notícia encontrada</h3>
                                <p class="text-muted">Ainda não temos notícias publicadas nesta seção.</p>
                            </div>
                        @endif
                        
                        <!-- Pagination -->
                        @if($posts->hasPages())
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-center">
                                        {{ $posts->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
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
    .page-header {
        background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
        border-radius: 15px;
        padding: 3rem 2rem;
        margin-bottom: 3rem;
        color: white;
    }
    
    .breaking-news-badge {
        margin-top: 1rem;
    }
    
    .pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .featured-news-card {
        position: relative;
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        height: 400px;
    }
    
    .featured-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .featured-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        color: white;
        padding: 2rem;
    }
    
    .breaking-badge {
        background: #dc3545;
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: bold;
        margin-right: 0.5rem;
    }
    
    .featured-title {
        font-size: 1.5rem;
        margin: 1rem 0;
        line-height: 1.3;
    }
    
    .featured-title a {
        color: white;
        text-decoration: none;
    }
    
    .latest-sidebar {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        height: 400px;
        overflow-y: auto;
    }
    
    .sidebar-title {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .sidebar-news-item {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f3f4;
    }
    
    .sidebar-news-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .sidebar-news-item img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
    }
    
    .sidebar-content h6 {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }
    
    .sidebar-content a {
        color: #2d3748;
        text-decoration: none;
    }
    
    .sidebar-content a:hover {
        color: #0984e3;
    }
    
    .news-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    
    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .news-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    .news-card-body {
        padding: 1.5rem;
    }
    
    .news-category {
        display: inline-block;
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .news-title {
        font-size: 1.1rem;
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
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>
@endsection