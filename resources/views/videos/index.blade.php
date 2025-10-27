@extends('layouts.blog')

@section('title', 'Vídeos')

@section('content')
    
    <!-- Video Header -->
    <div class="video-header-bg">
        <div class="container">
            <div class="row align-items-center py-4">
                <div class="col-md-8">
                    <h1 class="text-white mb-2">
                        <i class="fas fa-video me-2"></i>Vídeos
                    </h1>
                    <p class="text-white-50 mb-0">Assista aos nossos conteúdos em vídeo</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="video-stats">
                        <span class="badge bg-white text-dark px-3 py-2">
                            <i class="fas fa-play-circle me-1"></i>
                            {{ $totalVideos }} vídeos disponíveis
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container my-4">
        <!-- Filters Section -->
        <div class="filters-section mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="🔍 Buscar vídeos por título, descrição ou categoria..."
                                   value="{{ request('search') }}">
                            <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                @if($categories && count($categories) > 0)
                <div class="col-md-4">
                    <form method="GET">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="categoria" class="form-select" onchange="this.form.submit()">
                            <option value="">📂 Todas as categorias</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" 
                                        {{ request('categoria') === $category ? 'selected' : '' }}>
                                    🎬 {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @endif
                
                <div class="col-md-2">
                    @if(request('search') || request('categoria'))
                        <a href="{{ route('videos.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i>Limpar
                        </a>
                    @endif
                </div>
            </div>
            
            <!-- Active Filters Display -->
            @if(request('search') || request('categoria'))
                <div class="active-filters mt-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted small">Filtros ativos:</span>
                        
                        @if(request('search'))
                            <span class="badge bg-primary">
                                🔍 "{{ request('search') }}"
                                <a href="?categoria={{ request('categoria') }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                        
                        @if(request('categoria'))
                            <span class="badge bg-secondary">
                                📂 {{ ucfirst(request('categoria')) }}
                                <a href="?search={{ request('search') }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
            
        <!-- Videos Grid -->
        @if($videos->count() > 0)
            <div class="videos-grid">
                @foreach($videos as $video)
                    <div class="video-item">
                        <div class="video-card">
                            <div class="video-thumbnail">
                                <a href="{{ route('videos.show', $video) }}" class="video-link">
                                    <img src="{{ $video->thumbnail }}" 
                                         alt="{{ $video->title }}"
                                         class="video-image">
                                    
                                    <!-- Play Button Overlay -->
                                    <div class="video-overlay">
                                        <div class="play-button">
                                            <i class="fas fa-play"></i>
                                        </div>
                                    </div>
                                    
                                    @if($video->duration)
                                        <div class="video-duration">
                                            {{ $video->duration }}
                                        </div>
                                    @endif
                                    
                                    <!-- Platform Badge -->
                                    @if($video->video_platform)
                                        <div class="platform-badge">
                                            @if($video->video_platform === 'youtube')
                                                <i class="fab fa-youtube" style="color: #ff0000;"></i>
                                            @elseif($video->video_platform === 'vimeo')
                                                <i class="fab fa-vimeo" style="color: #1ab7ea;"></i>
                                            @else
                                                <i class="fas fa-video"></i>
                                            @endif
                                        </div>
                                    @endif
                                </a>
                            </div>
                            
                            <div class="video-content">
                                <h5 class="video-title">
                                    <a href="{{ route('videos.show', $video) }}">
                                        {{ $video->title }}
                                    </a>
                                </h5>
                                
                                <div class="video-description">
                                    @if($video->description)
                                        <p>{{ Str::limit($video->description, 80) }}</p>
                                    @else
                                        <p class="text-muted">Sem descrição disponível.</p>
                                    @endif
                                </div>
                                
                                <div class="video-meta mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="video-info">
                                            @if($video->category)
                                                <span class="category-tag">
                                                    {{ ucfirst($video->category) }}
                                                </span>
                                            @endif
                                            
                                            @if($video->published_date)
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $video->formatted_published_date }}
                                                </small>
                                            @endif
                                        </div>
                                        
                                        @if($video->views_count)
                                            <small class="text-muted">
                                                <i class="fas fa-eye me-1"></i>
                                                {{ number_format($video->views_count) }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
                
            <!-- Pagination -->
            @if($videos->hasPages())
                <div class="pagination-section mt-5">
                    <div class="d-flex justify-content-center">
                        <div class="pagination-wrapper">
                            {{ $videos->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="empty-videos">
                <div class="empty-content">
                    <i class="fas fa-video fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">
                        @if(request('search') || request('categoria'))
                            Nenhum vídeo encontrado
                        @else
                            Ainda não há vídeos
                        @endif
                    </h3>
                    <p class="text-muted mb-4">
                        @if(request('search') || request('categoria'))
                            Tente ajustar os filtros de busca ou explorar outras categorias.
                        @else
                            Os vídeos serão adicionados em breve. Volte mais tarde!
                        @endif
                    </p>
                    
                    @if(request('search') || request('categoria'))
                        <a href="{{ route('videos.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i>Ver todos os vídeos
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    <!-- Footer Spacing -->
    <div class="pb-5 mb-4"></div>

    <style>
        /* Video Header */
        .video-header-bg {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            min-height: 120px;
        }

        /* Filters Section */
        .filters-section {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #ff6b6b;
        }

        .active-filters .badge {
            font-size: 0.85rem;
            border-radius: 20px;
        }

        /* Videos Grid */
        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .video-item {
            transition: transform 0.3s ease;
        }

        .video-item:hover {
            transform: translateY(-5px);
        }

        .video-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .video-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .video-thumbnail {
            position: relative;
            overflow: hidden;
            aspect-ratio: 16/9;
            flex-shrink: 0;
        }

        .video-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .video-item:hover .video-image {
            transform: scale(1.05);
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .video-item:hover .video-overlay {
            opacity: 1;
        }

        .play-button {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ff6b6b;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .play-button:hover {
            background: white;
            transform: scale(1.1);
            color: #ee5a52;
        }

        .video-duration {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .platform-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255,255,255,0.9);
            padding: 6px 8px;
            border-radius: 20px;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .video-content {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .video-title {
            margin-bottom: 0.75rem;
            min-height: 3rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5rem;
        }

        .video-title a {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            line-height: 1.3;
            transition: color 0.3s ease;
        }

        .video-title a:hover {
            color: #ff6b6b;
        }

        .video-description {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 1rem;
            min-height: 4rem;
            flex-grow: 0;
        }

        .video-description p {
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .video-meta {
            font-size: 0.85rem;
            flex-shrink: 0;
            margin-top: auto !important;
        }

        .category-tag {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .video-link {
            text-decoration: none;
            color: inherit;
        }

        /* Empty State */
        .empty-videos {
            text-align: center;
            padding: 4rem 2rem;
            background: #f8f9fa;
            border-radius: 12px;
            margin: 2rem 0;
        }

        .empty-content {
            max-width: 500px;
            margin: 0 auto;
        }

        /* Pagination */
        .pagination-section {
            text-align: center;
        }

        .pagination-wrapper {
            background: #fff;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: inline-block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .videos-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 1rem;
            }
            
            .filters-section {
                padding: 1rem;
            }
            
            .video-content {
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            .videos-grid {
                grid-template-columns: 1fr;
            }
            
            .play-button {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }

        /* Search form improvements */
        .search-form .form-control:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
        }

        .btn-outline-primary {
            border-color: #ff6b6b;
            color: #ff6b6b;
        }

        .btn-outline-primary:hover {
            background-color: #ff6b6b;
            border-color: #ff6b6b;
            color: white;
        }
    </style>
@endsection