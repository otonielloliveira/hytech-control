@extends('layouts.blog')

@section('title', 'Álbuns de Fotos')

@section('content')
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Álbuns de Fotos</h1>
                <p class="text-center text-muted mb-5">Confira nossa galeria de fotos dos eventos</p>

                @if($albums->count() > 0)
                <div class="row g-4">
                    @foreach($albums as $album)
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card h-100 shadow-sm album-card">
                                <div class="position-relative album-cover">
                                    @if($album->cover_image_url)
                                        <a href="{{ route('albums.show', $album) }}">
                                            <img src="{{ $album->cover_image_url }}" 
                                                 class="card-img-top album-cover-image" 
                                                 alt="{{ $album->title }}"
                                                 style="height: 250px; object-fit: cover;">
                                        </a>
                                    @else
                                        <a href="{{ route('albums.show', $album) }}">
                                            <div class="bg-light d-flex align-items-center justify-content-center album-placeholder" 
                                                 style="height: 250px;">
                                                <i class="fas fa-images fa-3x text-muted"></i>
                                            </div>
                                        </a>
                                    @endif
                                    
                                    <!-- Photo Count Badge -->
                                    @if($album->photo_count > 0)
                                        <div class="position-absolute top-0 end-0 m-3">
                                            <span class="badge bg-dark bg-opacity-75 px-3 py-2">
                                                <i class="fas fa-image me-1"></i>
                                                {{ $album->photo_count }}
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <!-- Album Info Overlay -->
                                    <div class="album-overlay position-absolute bottom-0 start-0 w-100 p-4">
                                        <div class="album-overlay-content">
                                            <h5 class="text-white mb-2 fw-bold">{{ $album->title }}</h5>
                                            @if($album->event_date)
                                                <p class="text-white-50 mb-0 small">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $album->formatted_event_date }}
                                                </p>
                                            @endif
                                            @if($album->location)
                                                <p class="text-white-50 mb-0 small">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $album->location }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    @if($album->description)
                                        <p class="card-text text-muted flex-grow-1 mb-3">
                                            {{ Str::limit($album->description, 120) }}
                                        </p>
                                    @endif
                                    
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <a href="{{ route('albums.show', $album) }}" class="btn btn-primary">
                                            <i class="fas fa-eye me-1"></i>
                                            Ver Álbum
                                        </a>
                                        
                                        @if($album->photo_count > 0)
                                            <span class="text-muted small">
                                                {{ $album->photo_count }} {{ Str::plural('foto', $album->photo_count) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>                    <!-- Paginação -->
                    @if ($albums->hasPages())
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


        </div>
    </div>

    <style>
        .album-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            border: none;
        }

        .album-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .album-cover {
            overflow: hidden;
            position: relative;
        }

        .album-cover-image {
            transition: transform 0.4s ease;
            width: 100%;
        }

        .album-card:hover .album-cover-image {
            transform: scale(1.05);
        }

        .album-placeholder {
            transition: background-color 0.3s ease;
        }

        .album-card:hover .album-placeholder {
            background-color: #e9ecef !important;
        }

        .album-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 50%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .album-card:hover .album-overlay {
            opacity: 1;
        }

        .album-overlay-content {
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .album-card:hover .album-overlay-content {
            transform: translateY(0);
        }

        .album-cover a {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .sidebar {
            padding-top: 2rem;
        }

        .sidebar-widget {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            transition: box-shadow 0.3s ease;
        }

        .sidebar-widget:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .widget-header {
            margin-bottom: 1.2rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f8f9fa;
        }

        .widget-header h5 {
            margin: 0;
            color: #495057;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .widget-item {
            padding: 1rem 0;
            border-bottom: 1px solid #f8f9fa;
            transition: padding 0.3s ease;
        }

        .widget-item:hover {
            padding-left: 0.5rem;
        }

        .widget-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .widget-icon {
            color: #6c757d;
            width: 24px;
            text-align: center;
            font-size: 1.1rem;
        }

        .widget-info h6 a {
            color: #495057;
            text-decoration: none;
            font-size: 0.95rem;
            line-height: 1.4;
            transition: color 0.3s ease;
        }

        .widget-info h6 a:hover {
            color: #007bff;
        }

        .tag-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .tag-link {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            background: #f8f9fa;
            color: #6c757d;
            text-decoration: none;
            border-radius: 20px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .tag-link:hover {
            background: #007bff;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }

        .tag-count {
            font-size: 0.75rem;
            opacity: 0.8;
            margin-left: 0.2rem;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .album-overlay {
                opacity: 1;
                background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.5) 60%, transparent 100%);
            }
            
            .album-overlay-content {
                transform: translateY(0);
            }
            
            .album-card:hover {
                transform: translateY(-4px);
            }
        }
    </style>
@endsection
