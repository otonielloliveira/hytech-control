@extends('layouts.blog')

@section('title', $album->title . ' - Álbuns de Fotos')

@section('content')
    <!-- Hero Header com Imagem de Capa -->
    <div class="album-hero-header">
        <div class="album-hero-overlay"></div>
        <div class="album-hero-content">
            <div class="container-fluid">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb breadcrumb-custom">
                        <li class="breadcrumb-item">
                            <a href="{{ route('albums.index') }}">
                                <i class="fas fa-images me-1"></i>Álbuns
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ $album->title }}</li>
                    </ol>
                </nav>
                
                <h1 class="album-title">{{ $album->title }}</h1>
                
                @if ($album->description)
                    <p class="album-description">{{ $album->description }}</p>
                @endif
                
                <div class="album-meta">
                    @if ($album->event_date)
                        <span class="meta-item">
                            <i class="fas fa-calendar"></i>
                            {{ $album->formatted_event_date }}
                        </span>
                    @endif
                    <span class="meta-item">
                        <i class="fas fa-images"></i>
                        {{ count($photos) }} {{ Str::plural('foto', count($photos)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Section -->
    <div class="gallery-section">
        <div class="container-fluid px-lg-4">
            @if ($photos->count() > 0)
                <!-- Masonry Grid Layout -->
                <div class="photo-masonry-grid" id="photoGallery">
                    @foreach ($photos as $index => $photo)
                        <div class="photo-grid-item" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                            <div class="photo-card" 
                                 data-photo-index="{{ $index }}"
                                 data-photo-id="{{ $photo->id }}"
                                 data-photo-url="{{ $photo->image_url }}"
                                 data-photo-title="{{ e($photo->title ?: '') }}"
                                 data-photo-description="{{ e($photo->description ?: '') }}">
                                
                                <img src="{{ $photo->thumbnail_url }}"
                                     alt="{{ $photo->alt_text ?: $photo->title }}"
                                     class="photo-img"
                                     loading="lazy">
                                
                                @if ($photo->is_featured)
                                    <div class="photo-featured-badge">
                                        <i class="fas fa-star"></i>
                                    </div>
                                @endif
                                
                                <div class="photo-info-overlay">
                                    <div class="photo-info-content">
                                        <i class="fas fa-search-plus mb-2"></i>
                                        @if ($photo->title)
                                            <h6 class="photo-card-title">{{ $photo->title }}</h6>
                                        @endif
                                        @if ($photo->description)
                                            <p class="photo-card-desc">{{ Str::limit($photo->description, 60) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="empty-gallery">
                    <div class="empty-gallery-content">
                        <i class="fas fa-camera-retro"></i>
                        <h3>Álbum Vazio</h3>
                        <p>Este álbum ainda não possui fotos.</p>
                        <a href="{{ route('albums.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Ver Outros Álbuns
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Lightbox Modal -->
    <div class="lightbox-modal" id="lightboxModal">
        <button class="lightbox-close" id="closeLightbox">
            <i class="fas fa-times"></i>
        </button>
        
        <button class="lightbox-nav lightbox-prev" id="prevPhoto">
            <i class="fas fa-chevron-left"></i>
        </button>
        
        <button class="lightbox-nav lightbox-next" id="nextPhoto">
            <i class="fas fa-chevron-right"></i>
        </button>
        
        <div class="lightbox-content">
            <div class="lightbox-image-container">
                <img id="lightboxImage" src="" alt="">
                <div class="lightbox-loader">
                    <div class="spinner"></div>
                </div>
            </div>
            
            <div class="lightbox-info">
                <div class="lightbox-info-content">
                    <h3 id="lightboxTitle"></h3>
                    <p id="lightboxDescription"></p>
                    <div class="lightbox-counter" id="lightboxCounter"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Reset e Base */
        * {
            box-sizing: border-box;
        }

        body.lightbox-open {
            overflow: hidden;
        }

        /* Hero Header */
        .album-hero-header {
            position: relative;
            min-height: 350px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            overflow: hidden;
        }

        .album-hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        .album-hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 3rem 1rem;
            width: 100%;
        }

        .breadcrumb-custom {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            display: inline-flex;
            margin-bottom: 2rem;
        }

        .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.7);
        }

        .breadcrumb-custom a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .breadcrumb-custom a:hover {
            opacity: 0.8;
        }

        .breadcrumb-custom .active {
            color: rgba(255, 255, 255, 0.9);
        }

        .album-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.6s ease;
        }

        .album-description {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            opacity: 0.95;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 0.6s ease 0.2s both;
        }

        .album-meta {
            display: flex;
            gap: 2rem;
            justify-content: center;
            animation: fadeInUp 0.6s ease 0.4s both;
        }

        .meta-item {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-size: 0.95rem;
        }

        .meta-item i {
            margin-right: 0.5rem;
        }

        /* Gallery Section */
        .gallery-section {
            padding: 4rem 0;
            background: #f8f9fa;
        }

        /* Masonry Grid */
        .photo-masonry-grid {
            column-count: 4;
            column-gap: 1.5rem;
            margin: 0;
        }

        @media (max-width: 1400px) {
            .photo-masonry-grid {
                column-count: 3;
            }
        }

        @media (max-width: 992px) {
            .photo-masonry-grid {
                column-count: 2;
            }
        }

        @media (max-width: 576px) {
            .photo-masonry-grid {
                column-count: 1;
                column-gap: 1rem;
            }
        }

        .photo-grid-item {
            break-inside: avoid;
            margin-bottom: 1.5rem;
            page-break-inside: avoid;
        }

        /* Photo Card */
        .photo-card {
            position: relative;
            cursor: pointer;
            border-radius: 12px;
            overflow: hidden;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .photo-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .photo-img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.4s ease;
        }

        .photo-card:hover .photo-img {
            transform: scale(1.05);
        }

        /* Featured Badge */
        .photo-featured-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #ffc107;
            color: #000;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
            z-index: 3;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        /* Photo Info Overlay */
        .photo-info-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
            display: flex;
            align-items: flex-end;
            padding: 1.5rem;
            color: white;
        }

        .photo-card:hover .photo-info-overlay {
            opacity: 1;
        }

        .photo-info-content {
            transform: translateY(20px);
            transition: transform 0.4s ease;
        }

        .photo-card:hover .photo-info-content {
            transform: translateY(0);
        }

        .photo-info-content i {
            font-size: 1.5rem;
            opacity: 0.9;
        }

        .photo-card-title {
            margin: 0.5rem 0 0.25rem 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .photo-card-desc {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Empty Gallery */
        .empty-gallery {
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .empty-gallery-content {
            max-width: 500px;
            padding: 3rem;
        }

        .empty-gallery-content i {
            font-size: 5rem;
            color: #dee2e6;
            margin-bottom: 1.5rem;
        }

        .empty-gallery-content h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #495057;
        }

        .empty-gallery-content p {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }

        /* Lightbox Modal */
        .lightbox-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.98);
            z-index: 9999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .lightbox-modal.active {
            display: flex;
            opacity: 1;
        }

        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.5rem;
            z-index: 10;
            transition: all 0.3s ease;
        }

        .lightbox-close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.5rem;
            z-index: 10;
            transition: all 0.3s ease;
        }

        .lightbox-nav:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-50%) scale(1.1);
        }

        .lightbox-prev {
            left: 30px;
        }

        .lightbox-next {
            right: 30px;
        }

        .lightbox-nav:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .lightbox-content {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .lightbox-image-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        #lightboxImage {
            max-width: 90%;
            max-height: 85vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            transition: opacity 0.3s ease;
        }

        .lightbox-loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none; /* Escondido por padrão */
            z-index: 10;
        }

        .lightbox-loader.active {
            display: block;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .lightbox-info {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            text-align: center;
            color: white;
            border-radius: 12px;
            margin-top: 1rem;
        }

        .lightbox-info-content h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .lightbox-info-content p {
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .lightbox-counter {
            font-size: 0.9rem;
            opacity: 0.7;
            margin-top: 0.5rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .album-title {
                font-size: 2rem;
            }

            .album-description {
                font-size: 1rem;
            }

            .album-meta {
                flex-direction: column;
                gap: 0.5rem;
            }

            .gallery-section {
                padding: 2rem 0;
            }

            .lightbox-nav {
                width: 45px;
                height: 45px;
                font-size: 1.2rem;
            }

            .lightbox-prev {
                left: 10px;
            }

            .lightbox-next {
                right: 10px;
            }

            .lightbox-close {
                top: 10px;
                right: 10px;
                width: 40px;
                height: 40px;
            }

            #lightboxImage {
                max-width: 95%;
                max-height: 70vh;
            }

            .photo-grid-item {
                margin-bottom: 1rem;
            }
        }
    
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 120px;
        }

        /* Album Info Card */
        .album-info-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #667eea;
        }

        /* Photos Container - Smart Scroll after 2 rows */
        .photos-scrollable-container {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        /* Diferentes alturas baseadas no número de colunas por breakpoint */
        /* XL: 4 colunas = 2 linhas = altura para 8 fotos */
        @media (min-width: 1200px) {
            .photos-scrollable-container {
                max-height: calc(2 * (200px + 1rem) + 1.5rem);
                /* 2 linhas de 200px + gaps + padding */
                overflow-y: auto;
            }
        }

        /* LG: 3 colunas = 2 linhas = altura para 6 fotos */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .photos-scrollable-container {
                max-height: calc(2 * (200px + 1rem) + 1.5rem);
                overflow-y: auto;
            }
        }

        /* MD: 2 colunas = 2 linhas = altura para 4 fotos */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .photos-scrollable-container {
                max-height: calc(2 * (150px + 1rem) + 1.5rem);
                overflow-y: auto;
            }
        }

        /* SM: 2 colunas = 2 linhas = altura para 4 fotos */
        @media (max-width: 767.98px) {
            .photos-scrollable-container {
                max-height: calc(2 * (120px + 1rem) + 1.5rem);
                overflow-y: auto;
            }
        }

        .photos-scrollable-container::-webkit-scrollbar {
            width: 8px;
        }

        .photos-scrollable-container::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 4px;
        }

        .photos-scrollable-container::-webkit-scrollbar-thumb {
            background: #dee2e6;
            border-radius: 4px;
        }

        .photos-scrollable-container::-webkit-scrollbar-thumb:hover {
            background: #adb5bd;
        }

        .photo-item {
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .photo-item:hover {
            transform: translateY(-2px);
        }

        .photo-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
            aspect-ratio: 1;
            height: 200px;
            background: #f8f9fa;
        }

        .photo-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s ease;
            border-radius: 0.5rem;
        }

        .photo-item:hover .photo-thumbnail {
            transform: scale(1.05);
        }

        .photo-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: #ffc107;
            color: #212529;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 3;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg,
                    transparent 0%,
                    transparent 40%,
                    rgba(0, 0, 0, 0.3) 70%,
                    rgba(0, 0, 0, 0.8) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 12px;
            border-radius: 0.5rem;
            pointer-events: none;
        }

        .photo-item:hover .photo-overlay {
            opacity: 1;
        }

        .photo-overlay-content {
            text-align: center;
            color: white;
            transform: translateY(10px);
            transition: transform 0.3s ease;
        }

        .photo-item:hover .photo-overlay-content {
            transform: translateY(0);
        }

        .photo-title {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 4px;
        }

        /* Modal Improvements */
        .modal-body {
            background: #000;
        }

        #modalPhoto {
            border-radius: 8px;
        }

        .btn-outline-light {
            border-width: 2px;
            backdrop-filter: blur(10px);
            background: rgba(0, 0, 0, 0.3);
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: #fff;
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
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

        #prevPhoto,
        #nextPhoto {
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

        #prevPhoto:hover,
        #nextPhoto:hover {
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
                background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, transparent 60%);
            }
        }

        @media (max-width: 576px) {
            .photos-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lightboxModal = document.getElementById('lightboxModal');
            const lightboxImage = document.getElementById('lightboxImage');
            const lightboxTitle = document.getElementById('lightboxTitle');
            const lightboxDescription = document.getElementById('lightboxDescription');
            const lightboxCounter = document.getElementById('lightboxCounter');
            const lightboxLoader = document.querySelector('.lightbox-loader');
            const closeBtn = document.getElementById('closeLightbox');
            const prevBtn = document.getElementById('prevPhoto');
            const nextBtn = document.getElementById('nextPhoto');
            
            let photos = [];
            let currentIndex = 0;
            
            // Coletar todas as fotos
            document.querySelectorAll('.photo-card').forEach((card, index) => {
                photos.push({
                    url: card.dataset.photoUrl,
                    title: card.dataset.photoTitle,
                    description: card.dataset.photoDescription,
                    index: index
                });
                
                // Adicionar evento de clique
                card.addEventListener('click', function() {
                    openLightbox(index);
                });
            });
            
            function openLightbox(index) {
                currentIndex = index;
                updateLightbox();
                lightboxModal.classList.add('active');
                document.body.classList.add('lightbox-open');
            }
            
            function closeLightbox() {
                lightboxModal.classList.remove('active');
                document.body.classList.remove('lightbox-open');
            }
            
            function updateLightbox() {
                const photo = photos[currentIndex];
                
                // Mostrar loader e esconder imagem
                lightboxLoader.classList.add('active');
                lightboxImage.style.opacity = '0';
                lightboxImage.style.display = 'none';
                
                // Carregar imagem
                const img = new Image();
                
                img.onload = function() {
                    // Esconder loader
                    lightboxLoader.classList.remove('active');
                    
                    // Mostrar imagem
                    lightboxImage.src = photo.url;
                    lightboxImage.style.display = 'block';
                    
                    // Fade in suave
                    setTimeout(() => {
                        lightboxImage.style.opacity = '1';
                    }, 50);
                };
                
                img.onerror = function() {
                    // Em caso de erro, esconder loader e mostrar mensagem
                    lightboxLoader.classList.remove('active');
                    lightboxImage.style.display = 'block';
                    lightboxImage.style.opacity = '1';
                    console.error('Erro ao carregar imagem:', photo.url);
                };
                
                img.src = photo.url;
                
                // Atualizar informações
                lightboxTitle.textContent = photo.title || '{{ $album->title }}';
                lightboxDescription.textContent = photo.description || '';
                lightboxCounter.textContent = `${currentIndex + 1} / ${photos.length}`;
                
                // Atualizar botões de navegação
                prevBtn.disabled = currentIndex === 0;
                nextBtn.disabled = currentIndex === photos.length - 1;
                
                // Esconder descrição se vazia
                if (!photo.description) {
                    lightboxDescription.style.display = 'none';
                } else {
                    lightboxDescription.style.display = 'block';
                }
            }
            
            function navigatePhoto(direction) {
                if (direction === 'prev' && currentIndex > 0) {
                    currentIndex--;
                    updateLightbox();
                } else if (direction === 'next' && currentIndex < photos.length - 1) {
                    currentIndex++;
                    updateLightbox();
                }
            }
            
            // Event listeners
            closeBtn.addEventListener('click', closeLightbox);
            prevBtn.addEventListener('click', () => navigatePhoto('prev'));
            nextBtn.addEventListener('click', () => navigatePhoto('next'));
            
            // Fechar ao clicar fora da imagem
            lightboxModal.addEventListener('click', function(e) {
                if (e.target === lightboxModal) {
                    closeLightbox();
                }
            });
            
            // Navegação por teclado
            document.addEventListener('keydown', function(e) {
                if (!lightboxModal.classList.contains('active')) return;
                
                switch(e.key) {
                    case 'Escape':
                        closeLightbox();
                        break;
                    case 'ArrowLeft':
                        navigatePhoto('prev');
                        break;
                    case 'ArrowRight':
                        navigatePhoto('next');
                        break;
                }
            });
            
            // Suporte para touch gestures em mobile
            let touchStartX = 0;
            let touchEndX = 0;
            
            lightboxModal.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            });
            
            lightboxModal.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            });
            
            function handleSwipe() {
                const swipeThreshold = 50;
                const diff = touchStartX - touchEndX;
                
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        navigatePhoto('next');
                    } else {
                        navigatePhoto('prev');
                    }
                }
            }
        });
    </script>
@endsection
