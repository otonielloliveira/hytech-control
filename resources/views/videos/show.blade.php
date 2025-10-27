@extends('layouts.blog')

@section('title', $video->title . ' - Vídeos')

@section('content')


    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('videos.index') }}">Vídeos</a>
                        </li>
                        @if ($video->category)
                            <li class="breadcrumb-item">
                                <a href="{{ route('videos.index', ['categoria' => $video->category]) }}">
                                    {{ ucfirst($video->category) }}
                                </a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ Str::limit($video->title, 50) }}
                        </li>
                    </ol>
                </nav>

                <!-- Video Player -->
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <div class="ratio ratio-16x9">
                            @if ($video->embed_url)
                                <iframe src="{{ $video->embed_url }}" title="{{ $video->title }}" frameborder="0"
                                    allowfullscreen></iframe>
                            @else
                                <div class="d-flex align-items-center justify-content-center bg-light">
                                    <div class="text-center">
                                        <i class="fas fa-video fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Vídeo não disponível</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Video Info -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h1 class="h3 mb-3">{{ $video->title }}</h1>

                        <div class="d-flex flex-wrap gap-3 mb-3 text-muted">
                            @if ($video->formatted_published_date)
                                <span>
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $video->formatted_published_date }}
                                </span>
                            @endif

                            @if ($video->duration)
                                <span>
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $video->duration }}
                                </span>
                            @endif

                            <span>
                                <i class="fas fa-eye me-1"></i>
                                {{ number_format($video->views_count) }} visualizações
                            </span>

                            @if ($video->category)
                                <span class="badge bg-primary">{{ ucfirst($video->category) }}</span>
                            @endif
                        </div>

                        @if ($video->description)
                            <div class="video-description">
                                <p class="mb-0">{!! nl2br(e($video->description)) !!}</p>
                            </div>
                        @endif

                        @if (is_array($video->tags) && count($video->tags) > 0)
                            <div class="mt-3">
                                <strong>Tags:</strong>
                                @foreach ($video->tags as $tag)
                                    <span class="badge bg-secondary me-1">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Videos -->
                @if ($relatedVideos->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Vídeos Relacionados</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach ($relatedVideos as $relatedVideo)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 related-video-card">
                                            <div class="position-relative">
                                                <img src="{{ $relatedVideo->thumbnail }}" class="card-img-top"
                                                    alt="{{ $relatedVideo->title }}"
                                                    style="height: 120px; object-fit: cover;">

                                                <div class="position-absolute top-50 start-50 translate-middle">
                                                    <div class="play-button-small">
                                                        <i class="fas fa-play"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body p-2">
                                                <h6 class="card-title small mb-1">
                                                    <a href="{{ route('videos.show', $relatedVideo) }}"
                                                        class="text-decoration-none">
                                                        {{ Str::limit($relatedVideo->title, 60) }}
                                                    </a>
                                                </h6>

                                                <small class="text-muted">
                                                    @if ($relatedVideo->views_count > 0)
                                                        {{ number_format($relatedVideo->views_count) }} visualizações
                                                    @endif

                                                    @if ($relatedVideo->formatted_published_date)
                                                        • {{ $relatedVideo->formatted_published_date }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Mais Vídeos</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('videos.index') }}" class="btn btn-primary">
                                <i class="fas fa-video me-1"></i>
                                Ver Todos os Vídeos
                            </a>

                            @if ($video->category)
                                <a href="{{ route('videos.index', ['categoria' => $video->category]) }}"
                                    class="btn btn-outline-primary">
                                    <i class="fas fa-tag me-1"></i>
                                    Mais de {{ ucfirst($video->category) }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .related-video-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .related-video-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1) !important;
        }

        .play-button-small {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #007bff;
            transition: all 0.3s ease;
            opacity: 0;
        }

        .related-video-card:hover .play-button-small {
            opacity: 1;
            transform: scale(1.1);
        }

        .video-description {
            max-height: 200px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .video-description::-webkit-scrollbar {
            width: 6px;
        }

        .video-description::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .video-description::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .video-description::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endsection
