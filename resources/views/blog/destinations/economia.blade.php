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
                    <h1 class="display-4 fw-bold text-success">� {{ $title }}</h1>
                    <p class="lead text-muted">{{ $description }}</p>
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
                <!-- Posts -->
                <section class="section">
                    <div class="container-fluid">
                        <div class="row">
                            @forelse($posts as $post)
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
                                                <span>
                                                    <i class="fas fa-clock me-1"></i>{{ $post->reading_time }} min
                                                </span>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                        <h3>Nenhum artigo encontrado</h3>
                                        <p class="text-muted">Ainda não temos artigos publicados nesta seção.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        
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
        background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
        border-radius: 15px;
        padding: 3rem 2rem;
        margin-bottom: 3rem;
    }
    
    .post-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
        height: 100%;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
</style>
@endsection