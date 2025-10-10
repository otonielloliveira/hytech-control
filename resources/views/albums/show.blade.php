@extends('layouts.blog')

@section('title', $album->title . ' - Álbuns de Fotos')

@section('content')

    <!-- Compact Header -->
    <div class="album-header-bg">
        <div class="container">
            <div class="row align-items-center py-4">
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item">
                                <a href="{{ route('albums.index') }}" class="text-white-50">
                                    <i class="fas fa-images me-1"></i>Álbuns
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-white" aria-current="page">
                                {{ Str::limit($album->title, 30) }}
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-white mb-0">{{ $album->title }}</h1>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('albums.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>



    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Album Header -->
                <div class="mb-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('albums.index') }}">Álbuns</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ $album->title }}
                            </li>
                        </ol>
                    </nav>

                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-2">{{ $album->title }}</h1>

                            @if ($album->description)
                                <p class="text-muted mb-3">{{ $album->description }}</p>
                            @endif

                            <div class="d-flex flex-wrap gap-3 small text-muted">
                                @if ($album->event_date)
                                    <span>
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $album->formatted_event_date }}
                                    </span>
                                @endif

                                @if ($album->location)
                                    <span>
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $album->location }}
                                    </span>
                                @endif

                                <span>
                                    <i class="fas fa-images me-1"></i>
                                    {{ $photos->total() }} {{ Str::plural('foto', $photos->total()) }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4 text-md-end">
                            <button type="button" id="testModal" class="btn btn-info btn-sm me-2">
                                <i class="fas fa-test"></i> Ver Fotos do Álbum
                            </button>
                            <a href="{{ route('albums.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Voltar aos Álbuns
                            </a>
                        </div>
                    </div>
                </div>

        <!-- Photos Grid -->
        @if ($photos->count() > 0)
            <div class="photos-grid" id="photosGrid">
                @foreach ($photos as $index => $photo)
                    <div class="photo-item">
                        <div class="photo-wrapper">
                            <img src="{{ $photo->thumbnail_url }}" 
                                 class="photo-thumbnail photo-clickable"
                                 alt="{{ $photo->alt_text ?: $photo->title }}" 
                                 data-photo-index="{{ $index }}"
                                 data-photo-id="{{ $photo->id }}"
                                 data-photo-url="{{ $photo->image_url }}"
                                 data-photo-title="{{ e($photo->title ?: '') }}"
                                 data-photo-description="{{ e($photo->description ?: '') }}">

                            @if ($photo->is_featured)
                                <div class="photo-badge">
                                    <i class="fas fa-star"></i>
                                </div>
                            @endif

                            <div class="photo-overlay">
                                <div class="photo-overlay-content">
                                    <i class="fas fa-expand fa-lg"></i>
                                    @if ($photo->title)
                                        <span class="photo-title">{{ Str::limit($photo->title, 20) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>            <!-- Pagination -->
            @if ($photos->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    <div class="pagination-wrapper">
                        {{ $photos->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="empty-album">
                <div class="empty-album-content">
                    <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted mb-2">Álbum vazio</h4>
                    <p class="text-muted">Este álbum ainda não possui fotos.</p>
                    <a href="{{ route('albums.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Ver outros álbuns
                    </a>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Footer Spacing -->
    <div class="pb-5 mb-4"></div>

    <!-- Photo Modal with Slider -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white" id="photoModalLabel"></h5>
                    <div class="d-flex align-items-center gap-2">
                        <span id="photoCounter" class="text-white small"></span>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0 text-center position-relative">
                    <img id="modalPhoto" class="img-fluid" alt="" style="max-height: 80vh; width: auto;">
                    
                    <!-- Navigation Buttons -->
                    <button type="button" id="prevPhoto" class="btn btn-outline-light position-absolute top-50 start-0 translate-middle-y ms-3" style="z-index: 10;">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" id="nextPhoto" class="btn btn-outline-light position-absolute top-50 end-0 translate-middle-y me-3" style="z-index: 10;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <p id="photoDescription" class="text-white mb-0"></p>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Album Header */
        .album-header-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 120px;
        }

        /* Album Info Card */
        .album-info-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
        }

        /* Photos Grid */
        .photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .photo-item {
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .photo-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .photo-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            aspect-ratio: 4/3;
        }

        .photo-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .photo-item:hover .photo-thumbnail {
            transform: scale(1.05);
        }

        .photo-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #333;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            z-index: 3;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            display: flex;
            align-items: flex-end;
            padding: 1rem;
            z-index: 2;
        }

        .photo-item:hover .photo-overlay {
            opacity: 1;
        }

        .photo-overlay-content {
            color: white;
            text-align: center;
            width: 100%;
        }

        .photo-overlay-content i {
            display: block;
            margin-bottom: 0.5rem;
        }

        .photo-title {
            font-size: 0.9rem;
            font-weight: 500;
            display: block;
        }

        .photo-clickable {
            cursor: pointer !important;
            pointer-events: auto !important;
            position: relative;
            z-index: 1;
        }

        /* Empty Album */
        .empty-album {
            text-align: center;
            padding: 4rem 2rem;
            background: #f8f9fa;
            border-radius: 12px;
            margin: 2rem 0;
        }

        .empty-album-content {
            max-width: 400px;
            margin: 0 auto;
        }

        /* Pagination */
        .pagination-wrapper {
            background: #fff;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        /* Modal Improvements */
        #photoModal .modal-content {
            background: rgba(0, 0, 0, 0.95) !important;
            border: none;
            border-radius: 0;
        }

        #photoModal img {
            max-height: 85vh;
            width: auto;
            border-radius: 8px;
        }

        #prevPhoto, #nextPhoto {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1) !important;
            border: 2px solid rgba(255, 255, 255, 0.3) !important;
        }

        #prevPhoto:hover, #nextPhoto:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: scale(1.1);
            border-color: rgba(255, 255, 255, 0.5) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .photos-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 0.75rem;
            }
            
            .album-info-card {
                padding: 1rem;
            }
            
            .photo-overlay {
                opacity: 1;
                background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 60%);
            }
        }

        @media (max-width: 576px) {
            .photos-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
    </style>

    <script>
        // Debug: Verificar se há fotos na página
        console.log('Total de fotos na página:', document.querySelectorAll('.photo-clickable').length);
        console.log('Elementos com data-photo-index:', document.querySelectorAll('[data-photo-index]').length);
        
        // Aguardar que tudo esteja carregado
        window.addEventListener('load', function() {
            console.log('Page loaded, initializing photo gallery...');
            
            const photoModal = document.getElementById('photoModal');
            const modalPhoto = document.getElementById('modalPhoto');
            const modalTitle = document.getElementById('photoModalLabel');
            const photoDescription = document.getElementById('photoDescription');
            const photoCounter = document.getElementById('photoCounter');
            const prevBtn = document.getElementById('prevPhoto');
            const nextBtn = document.getElementById('nextPhoto');
            
            if (!photoModal || !modalPhoto || !modalTitle) {
                console.error('Modal elements not found');
                return;
            }
            
            // Array to store all photos data
            let photosData = [];
            let currentPhotoIndex = 0;
            let modalInstance = null;
            
            // Initialize photos data from the grid
            function initializePhotosData() {
                const photoElements = document.querySelectorAll('.photo-clickable[data-photo-index]');
                photosData = Array.from(photoElements).map((element, idx) => ({
                    id: element.getAttribute('data-photo-id'),
                    url: element.getAttribute('data-photo-url'),
                    title: element.getAttribute('data-photo-title') || '',
                    description: element.getAttribute('data-photo-description') || '',
                    index: parseInt(element.getAttribute('data-photo-index')) || idx
                }));
                console.log('Photos data initialized:', photosData.length, 'photos found');
                return photosData.length > 0;
            }
            
            // Display photo by index
            function showPhoto(index) {
                if (index < 0 || index >= photosData.length) {
                    console.log('Invalid photo index:', index);
                    return;
                }
                
                currentPhotoIndex = index;
                const photo = photosData[index];
                
                console.log('Showing photo:', index, photo);
                
                modalPhoto.src = photo.url;
                modalTitle.textContent = photo.title || '{{ $album->title }}';
                photoDescription.textContent = photo.description || '';
                photoCounter.textContent = `${index + 1} / ${photosData.length}`;
                
                if (!photo.description) {
                    photoDescription.style.display = 'none';
                } else {
                    photoDescription.style.display = 'block';
                }
                
                // Update navigation buttons
                if (prevBtn) {
                    prevBtn.style.display = index > 0 ? 'block' : 'none';
                }
                if (nextBtn) {
                    nextBtn.style.display = index < photosData.length - 1 ? 'block' : 'none';
                }
            }
            
            // Initialize data and setup
            if (!initializePhotosData()) {
                console.error('No photos found to initialize');
                return;
            }
            
            // Try to create modal instance
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    modalInstance = new bootstrap.Modal(photoModal);
                    console.log('Bootstrap modal instance created');
                } else {
                    console.warn('Bootstrap not available, using fallback');
                }
            } catch (error) {
                console.error('Error creating modal instance:', error);
            }
            
            // Add click event to all photo elements
            document.querySelectorAll('.photo-clickable').forEach((element, index) => {
                console.log('Adding click event to photo:', index, element);
                
                // Múltiplos tipos de eventos para garantir que funcione
                ['click', 'touchstart'].forEach(eventType => {
                    element.addEventListener(eventType, function(e) {
                        console.log('Event triggered:', eventType, 'on photo:', index);
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const photoIndex = parseInt(this.getAttribute('data-photo-index')) || index;
                        console.log('Photo clicked, index:', photoIndex);
                        
                        showPhoto(photoIndex);
                        
                        // Show modal
                        if (modalInstance) {
                            console.log('Showing modal with Bootstrap');
                            modalInstance.show();
                        } else {
                            console.log('Showing modal with fallback');
                            // Fallback for showing modal
                            photoModal.classList.add('show');
                            photoModal.style.display = 'block';
                            document.body.classList.add('modal-open');
                        }
                    });
                });
                
                // Também adicionar um evento de teste simples
                element.addEventListener('mouseenter', function() {
                    console.log('Mouse entered photo:', index);
                });
            });
            
            // Navigation button events
            if (prevBtn) {
                prevBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (currentPhotoIndex > 0) {
                        showPhoto(currentPhotoIndex - 1);
                    }
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (currentPhotoIndex < photosData.length - 1) {
                        showPhoto(currentPhotoIndex + 1);
                    }
                });
            }
            
            // Close button
            const closeBtn = photoModal.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    if (modalInstance) {
                        modalInstance.hide();
                    } else {
                        photoModal.classList.remove('show');
                        photoModal.style.display = 'none';
                        document.body.classList.remove('modal-open');
                    }
                });
            }
            
            // Keyboard navigation
            document.addEventListener('keydown', function(event) {
                if (photoModal.classList.contains('show')) {
                    if (event.key === 'ArrowLeft' && currentPhotoIndex > 0) {
                        showPhoto(currentPhotoIndex - 1);
                    } else if (event.key === 'ArrowRight' && currentPhotoIndex < photosData.length - 1) {
                        showPhoto(currentPhotoIndex + 1);
                    } else if (event.key === 'Escape') {
                        if (modalInstance) {
                            modalInstance.hide();
                        } else {
                            photoModal.classList.remove('show');
                            photoModal.style.display = 'none';
                            document.body.classList.remove('modal-open');
                        }
                    }
                }
            });
            
            console.log('Photo gallery initialized successfully');
            
            // Adicionar evento de teste
            const testBtn = document.getElementById('testModal');
            if (testBtn) {
                testBtn.addEventListener('click', function() {
                    console.log('Test button clicked');
                    if (photosData.length > 0) {
                        showPhoto(0);
                        if (modalInstance) {
                            modalInstance.show();
                        } else {
                            photoModal.classList.add('show');
                            photoModal.style.display = 'block';
                            document.body.classList.add('modal-open');
                        }
                    } else {
                        alert('Nenhuma foto encontrada para testar');
                    }
                });
            }
        });
    </script>
@endsection
