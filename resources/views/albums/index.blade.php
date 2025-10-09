@extends('layouts.blog')

@section('title', 'Álbuns de Fotos')

@section('content')
    <!-- Banner Carousel -->
    @php
        $banners = App\Models\Banner::where('is_active', true)->orderBy('sort_order')->get();
    @endphp
    @if($banners->count() > 0)
        <div class="hero-carousel">
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
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

<div class="container mt-4">
    <div class="row">
        <!-- Conteúdo Principal -->
        <div class="col-lg-8">
            <h1 class="text-center mb-4">Álbuns de Fotos</h1>
            <p class="text-center text-muted mb-5">Confira nossa galeria de fotos dos eventos</p>
            
            @if($albums->count() > 0)
                <div class="row g-4">
                    @foreach($albums as $album)
                        <div class="col-lg-6 col-md-6">
                            <div class="card h-100 shadow-sm album-card">
                                <div class="position-relative">
                                    @if($album->cover_image_url)
                                        <img src="{{ $album->cover_image_url }}" 
                                             class="card-img-top" 
                                             alt="{{ $album->title }}"
                                             style="height: 250px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 250px;">
                                            <i class="fas fa-images fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    @if($album->photo_count > 0)
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-dark bg-opacity-75">
                                                <i class="fas fa-image me-1"></i>
                                                {{ $album->photo_count }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $album->title }}</h5>
                                    
                                    @if($album->description)
                                        <p class="card-text text-muted flex-grow-1">
                                            {{ Str::limit($album->description, 100) }}
                                        </p>
                                    @endif
                                    
                                    <div class="mt-auto">
                                        @if($album->event_date)
                                            <small class="text-muted d-block mb-2">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $album->formatted_event_date }}
                                            </small>
                                        @endif
                                        
                                        @if($album->location)
                                            <small class="text-muted d-block mb-3">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $album->location }}
                                            </small>
                                        @endif
                                        
                                        <a href="{{ route('albums.show', $album) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>
                                            Ver Álbum
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Paginação -->
                @if($albums->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $albums->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-images fa-4x text-muted mb-3"></i>
                    <h3 class="text-muted">Nenhum álbum encontrado</h3>
                    <p class="text-muted">Ainda não há álbuns de fotos disponíveis.</p>
                </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sidebar">
                <!-- Widget de Downloads -->
                <div class="sidebar-widget mb-4">
                    <div class="widget-header">
                        <h5><i class="fas fa-download me-2"></i>Downloads Recentes</h5>
                    </div>
                    <div class="widget-content">
                        @php
                            $recentDownloads = App\Models\Download::where('is_active', true)
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        @if($recentDownloads->count() > 0)
                            @foreach($recentDownloads as $download)
                                <div class="widget-item">
                                    <div class="d-flex align-items-start">
                                        <div class="widget-icon me-2">
                                            <i class="{{ $download->icon_class ?? 'fas fa-file' }}"></i>
                                        </div>
                                        <div class="widget-info flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('downloads.show', $download) }}">
                                                    {{ Str::limit($download->title, 40) }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                {{ $download->download_count }} downloads
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Nenhum download disponível.</p>
                        @endif
                    </div>
                </div>

                <!-- Widget de Palestras -->
                <div class="sidebar-widget mb-4">
                    <div class="widget-header">
                        <h5><i class="fas fa-microphone me-2"></i>Próximas Palestras</h5>
                    </div>
                    <div class="widget-content">
                        @php
                            $upcomingLectures = App\Models\Lecture::where('is_active', true)
                                ->where('date_time', '>=', now())
                                ->orderBy('date_time', 'asc')
                                ->limit(3)
                                ->get();
                        @endphp
                        
                        @if($upcomingLectures->count() > 0)
                            @foreach($upcomingLectures as $lecture)
                                <div class="widget-item">
                                    <h6 class="mb-1">
                                        <a href="{{ route('lectures.show', $lecture) }}">
                                            {{ Str::limit($lecture->title, 50) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $lecture->date_time->format('d/m/Y - H:i') }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $lecture->speaker }}
                                    </small>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Nenhuma palestra programada.</p>
                        @endif
                    </div>
                </div>

                <!-- Widget de Tags -->
                <div class="sidebar-widget">
                    <div class="widget-header">
                        <h5><i class="fas fa-tags me-2"></i>Tags Populares</h5>
                    </div>
                    <div class="widget-content">
                        @php
                            $popularTags = App\Models\Tag::withCount('posts')
                                ->orderBy('posts_count', 'desc')
                                ->limit(10)
                                ->get();
                        @endphp
                        
                        @if($popularTags->count() > 0)
                            <div class="tag-cloud">
                                @foreach($popularTags as $tag)
                                    <a href="{{ route('blog.tag.show', $tag->slug) }}" class="tag-link">
                                        {{ $tag->name }}
                                        <span class="tag-count">({{ $tag->posts_count }})</span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Nenhuma tag encontrada.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.album-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.album-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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