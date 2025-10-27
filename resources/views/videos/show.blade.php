@extends('layouts.blog')

@section('title', $video->title . ' - Vídeos')

@section('content')
    <div class="video-page-container">
        <div class="container-fluid py-4">
            <div class="row g-4">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb-custom">
                            <li class="breadcrumb-item">
                                <a href="{{ route('videos.index') }}">
                                    <i class="fas fa-video me-1"></i>Vídeos
                                </a>
                            </li>
                            @if ($video->category)
                                <li class="breadcrumb-item">
                                    <a href="{{ route('videos.index', ['categoria' => $video->category]) }}">
                                        {{ ucfirst($video->category) }}
                                    </a>
                                </li>
                            @endif
                            <li class="breadcrumb-item active">
                                {{ Str::limit($video->title, 40) }}
                            </li>
                        </ol>
                    </nav>

                    <!-- Video Player Card -->
                    <div class="video-player-card mb-4">
                        <div class="video-player-wrapper">
                            @if ($video->embed_url)
                                <iframe src="{{ $video->embed_url }}" title="{{ $video->title }}" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            @else
                                <div class="video-placeholder">
                                    <i class="fas fa-video-slash fa-4x mb-3"></i>
                                    <p class="mb-0">Vídeo não disponível</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Video Info Card -->
                    <div class="video-info-card mb-4">
                        <h1 class="video-title">{{ $video->title }}</h1>

                        <div class="video-stats">
                            <div class="stat-item">
                                <i class="fas fa-eye"></i>
                                <span>{{ number_format($video->views_count) }} visualizações</span>
                            </div>
                            @if ($video->formatted_published_date)
                                <div class="stat-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ $video->formatted_published_date }}</span>
                                </div>
                            @endif
                            @if ($video->duration)
                                <div class="stat-item">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $video->duration }}</span>
                                </div>
                            @endif
                            @if ($video->category)
                                <div class="stat-item">
                                    <span class="category-badge">{{ ucfirst($video->category) }}</span>
                                </div>
                            @endif
                        </div>

                        @if ($video->description)
                            <div class="video-description-section">
                                <h5 class="section-title">
                                    <i class="fas fa-align-left me-2"></i>Descrição
                                </h5>
                                <div class="video-description-content">
                                    <p>{!! nl2br(e($video->description)) !!}</p>
                                </div>
                            </div>
                        @endif

                        @if (is_array($video->tags) && count($video->tags) > 0)
                            <div class="video-tags-section">
                                <h5 class="section-title">
                                    <i class="fas fa-tags me-2"></i>Tags
                                </h5>
                                <div class="tags-container">
                                    @foreach ($video->tags as $tag)
                                        <span class="video-tag">
                                            <i class="fas fa-hashtag"></i>{{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Related Videos Section -->
                    @if ($relatedVideos->count() > 0)
                        <div class="related-videos-section">
                            <h3 class="section-header">
                                <i class="fas fa-film me-2"></i>
                                Vídeos Relacionados
                            </h3>
                            <div class="related-videos-grid">
                                @foreach ($relatedVideos as $relatedVideo)
                                    <a href="{{ route('videos.show', $relatedVideo) }}" class="related-video-card">
                                        <div class="related-video-thumbnail">
                                            <img src="{{ $relatedVideo->thumbnail }}" alt="{{ $relatedVideo->title }}">
                                            <div class="play-overlay">
                                                <i class="fas fa-play"></i>
                                            </div>
                                            @if ($relatedVideo->duration)
                                                <span class="duration-badge">{{ $relatedVideo->duration }}</span>
                                            @endif
                                        </div>
                                        <div class="related-video-info">
                                            <h6 class="related-video-title">{{ $relatedVideo->title }}</h6>
                                            <div class="related-video-meta">
                                                @if ($relatedVideo->category)
                                                    <span class="meta-category">{{ ucfirst($relatedVideo->category) }}</span>
                                                @endif
                                                <span class="meta-views">
                                                    {{ number_format($relatedVideo->views_count) }} views
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Actions Card -->
                    <div class="sidebar-card mb-4">
                        <h5 class="sidebar-card-title">
                            <i class="fas fa-bolt me-2"></i>Ações Rápidas
                        </h5>
                        <div class="sidebar-card-body">
                            <a href="{{ route('videos.index') }}" class="sidebar-btn">
                                <i class="fas fa-th-large me-2"></i>
                                Todos os Vídeos
                            </a>
                            @if ($video->category)
                                <a href="{{ route('videos.index', ['categoria' => $video->category]) }}"
                                    class="sidebar-btn">
                                    <i class="fas fa-folder me-2"></i>
                                    Mais de {{ ucfirst($video->category) }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Share Card -->
                    <div class="sidebar-card">
                        <h5 class="sidebar-card-title">
                            <i class="fas fa-share-alt me-2"></i>Compartilhar
                        </h5>
                        <div class="sidebar-card-body">
                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                    target="_blank" class="share-btn facebook" title="Compartilhar no Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($video->title) }}"
                                    target="_blank" class="share-btn twitter" title="Compartilhar no Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($video->title) }}"
                                    target="_blank" class="share-btn linkedin" title="Compartilhar no LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="https://api.whatsapp.com/send?text={{ urlencode($video->title . ' - ' . request()->url()) }}"
                                    target="_blank" class="share-btn whatsapp" title="Compartilhar no WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .video-page-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        /* Breadcrumb */
        .breadcrumb-custom {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 0.9rem;
            display: flex;
            flex-wrap: wrap;
            list-style: none;
        }

        .breadcrumb-custom .breadcrumb-item {
            display: inline-flex;
            align-items: center;
        }

        .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
            content: '›';
            color: #6c757d;
            padding: 0 0.5rem;
        }

        .breadcrumb-custom a {
            color: #495057;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb-custom a:hover {
            color: #007bff;
        }

        .breadcrumb-custom .active {
            color: #6c757d;
        }

        /* Video Player Card */
        .video-player-card {
            background: #000;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .video-player-wrapper {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
        }

        .video-player-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .video-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        /* Video Info Card */
        .video-info-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .video-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1.25rem;
            line-height: 1.3;
        }

        .video-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6c757d;
            font-size: 0.95rem;
        }

        .stat-item i {
            color: #007bff;
            font-size: 1.1rem;
        }

        .category-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .section-title i {
            color: #007bff;
        }

        .video-description-section {
            margin-bottom: 1.5rem;
        }

        .video-description-content {
            background: #f8f9fa;
            padding: 1.25rem;
            border-radius: 12px;
            border-left: 4px solid #007bff;
            line-height: 1.7;
            color: #495057;
        }

        .video-description-content p {
            margin: 0;
        }

        .video-tags-section {
            padding-top: 1.5rem;
            border-top: 2px solid #f0f0f0;
        }

        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .video-tag {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .video-tag:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .video-tag i {
            font-size: 0.75rem;
        }

        /* Related Videos */
        .related-videos-section {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .section-header {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .section-header i {
            color: #007bff;
        }

        .related-videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .related-video-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }

        .related-video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .related-video-thumbnail {
            position: relative;
            padding-bottom: 56.25%;
            overflow: hidden;
            background: #000;
        }

        .related-video-thumbnail img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .related-video-card:hover .related-video-thumbnail img {
            transform: scale(1.05);
        }

        .play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            color: #007bff;
            font-size: 1.2rem;
        }

        .related-video-card:hover .play-overlay {
            opacity: 1;
        }

        .duration-badge {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .related-video-info {
            padding: 1rem;
        }

        .related-video-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .related-video-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #6c757d;
        }

        .meta-category {
            background: #e9ecef;
            padding: 0.25rem 0.6rem;
            border-radius: 12px;
            font-weight: 500;
        }

        /* Sidebar */
        .sidebar-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .sidebar-card-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.25rem;
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .sidebar-card-body {
            padding: 1.25rem;
        }

        .sidebar-btn {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.875rem 1rem;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            color: #495057;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 0.75rem;
        }

        .sidebar-btn:last-child {
            margin-bottom: 0;
        }

        .sidebar-btn:hover {
            background: #007bff;
            border-color: #007bff;
            color: white;
            transform: translateX(5px);
        }

        .sidebar-btn i {
            font-size: 1.1rem;
        }

        /* Share Buttons */
        .share-buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
        }

        .share-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 45px;
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .share-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .share-btn.facebook {
            background: #1877f2;
        }

        .share-btn.twitter {
            background: #1da1f2;
        }

        .share-btn.linkedin {
            background: #0077b5;
        }

        .share-btn.whatsapp {
            background: #25d366;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .video-title {
                font-size: 1.4rem;
            }

            .video-stats {
                gap: 1rem;
            }

            .related-videos-grid {
                grid-template-columns: 1fr;
            }

            .share-buttons {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endsection
