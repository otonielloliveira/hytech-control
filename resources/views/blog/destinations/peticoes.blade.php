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
                    <h1 class="display-4 fw-bold text-danger">✊ {{ $title }}</h1>
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
                                <div class="col-lg-6 mb-5">
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
                                            
                                            <!-- Videos da Petição -->
                                            @if($post->petition_videos && count($post->petition_videos) > 0)
                                                <div class="petition-videos mb-3">
                                                    <h5 class="mb-3"><i class="fas fa-video text-danger"></i> Vídeos da Campanha</h5>
                                                    <div class="row">
                                                        @foreach($post->petition_videos as $video)
                                                            <div class="col-md-6 mb-3">
                                                                <div class="video-item">
                                                                    <h6 class="video-title">{{ $video['titulo'] }}</h6>
                                                                    @if($video['tipo'] === 'youtube')
                                                                        @php
                                                                            $videoId = '';
                                                                            if (strpos($video['url'], 'youtube.com/watch?v=') !== false) {
                                                                                $videoId = substr($video['url'], strpos($video['url'], 'v=') + 2);
                                                                            } elseif (strpos($video['url'], 'youtu.be/') !== false) {
                                                                                $videoId = substr($video['url'], strpos($video['url'], 'youtu.be/') + 9);
                                                                            }
                                                                        @endphp
                                                                        @if($videoId)
                                                                            <iframe 
                                                                                width="100%" 
                                                                                height="200" 
                                                                                src="https://www.youtube.com/embed/{{ $videoId }}" 
                                                                                frameborder="0" 
                                                                                allowfullscreen
                                                                                class="rounded">
                                                                            </iframe>
                                                                        @endif
                                                                    @elseif($video['tipo'] === 'vimeo')
                                                                        @php
                                                                            $videoId = substr($video['url'], strrpos($video['url'], '/') + 1);
                                                                        @endphp
                                                                        <iframe 
                                                                            src="https://player.vimeo.com/video/{{ $videoId }}" 
                                                                            width="100%" 
                                                                            height="200" 
                                                                            frameborder="0" 
                                                                            allowfullscreen
                                                                            class="rounded">
                                                                        </iframe>
                                                                    @else
                                                                        <video controls width="100%" height="200" class="rounded">
                                                                            <source src="{{ $video['url'] }}" type="video/mp4">
                                                                            Seu navegador não suporta vídeos.
                                                                        </video>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- Grupos WhatsApp -->
                                            @if($post->whatsapp_groups && count($post->whatsapp_groups) > 0)
                                                <div class="whatsapp-groups mb-3">
                                                    <h5 class="mb-3"><i class="fab fa-whatsapp text-success"></i> Grupos WhatsApp por Região</h5>
                                                    <div class="row">
                                                        @foreach($post->whatsapp_groups as $group)
                                                            @if($group['status'] === 'ativo')
                                                                <div class="col-md-6 mb-2">
                                                                    <a href="{{ $group['link_grupo'] }}" 
                                                                       target="_blank" 
                                                                       class="btn btn-success btn-sm w-100">
                                                                        <i class="fab fa-whatsapp me-2"></i>
                                                                        {{ $group['estado'] }} - {{ $group['nome_grupo'] }}
                                                                    </a>
                                                                </div>
                                                            @elseif($group['status'] === 'cheio')
                                                                <div class="col-md-6 mb-2">
                                                                    <button class="btn btn-secondary btn-sm w-100" disabled>
                                                                        <i class="fas fa-users me-2"></i>
                                                                        {{ $group['estado'] }} - Grupo Cheio
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <div class="petition-meta">
                                                <span>
                                                    <i class="fas fa-user me-1"></i>{{ $post->user->name }}
                                                </span>
                                                <span>
                                                    <i class="fas fa-calendar me-1"></i>{{ $post->published_at->format('d/m/Y') }}
                                                </span>
                                            </div>
                                            
                                            <div class="petition-action mt-3">
                                                <a href="{{ route('blog.post.show', $post->slug) }}" 
                                                   class="btn btn-danger btn-lg w-100">
                                                    <i class="fas fa-hand-fist me-2"></i>Apoiar esta Petição
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="fas fa-hand-fist fa-3x text-muted mb-3"></i>
                                        <h3>Nenhuma petição encontrada</h3>
                                        <p class="text-muted">Ainda não temos petições ativas nesta seção.</p>
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
        background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%);
        border-radius: 15px;
        padding: 3rem 2rem;
        margin-bottom: 3rem;
    }
    
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
        height: 250px;
        object-fit: cover;
    }
    
    .petition-card-body {
        padding: 2rem;
    }
    
    .petition-category {
        display: inline-block;
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .petition-title {
        font-size: 1.4rem;
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
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }
    
    .petition-videos h5,
    .whatsapp-groups h5 {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }
    
    .video-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 10px;
        height: 100%;
    }
    
    .video-title {
        font-size: 0.9rem;
        margin-bottom: 0.8rem;
        color: #495057;
    }
    
    .petition-meta {
        display: flex;
        gap: 1.5rem;
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    
    .petition-action .btn {
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endsection