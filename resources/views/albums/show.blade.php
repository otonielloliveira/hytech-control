@extends('layouts.blog')

@section('title', $album->title . ' - Álbuns de Fotos')

@section('content')
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
                        
                        @if($album->description)
                            <p class="text-muted mb-3">{{ $album->description }}</p>
                        @endif
                        
                        <div class="d-flex flex-wrap gap-3 small text-muted">
                            @if($album->event_date)
                                <span>
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $album->formatted_event_date }}
                                </span>
                            @endif
                            
                            @if($album->location)
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
                        <a href="{{ route('albums.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Voltar aos Álbuns
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Photos Grid -->
            @if($photos->count() > 0)
                <div class="row g-3" id="photosGrid">
                    @foreach($photos as $photo)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card photo-card h-100">
                                <div class="position-relative overflow-hidden">
                                    <img src="{{ $photo->thumbnail_url }}" 
                                         class="card-img-top photo-thumbnail" 
                                         alt="{{ $photo->alt_text ?: $photo->title }}"
                                         data-bs-toggle="modal" 
                                         data-bs-target="#photoModal"
                                         data-photo-id="{{ $photo->id }}"
                                         data-photo-url="{{ $photo->image_url }}"
                                         data-photo-title="{{ $photo->title }}"
                                         data-photo-description="{{ $photo->description }}"
                                         style="height: 200px; object-fit: cover; cursor: pointer;">
                                    
                                    @if($photo->is_featured)
                                        <div class="position-absolute top-0 start-0 m-2">
                                            <span class="badge bg-warning">
                                                <i class="fas fa-star"></i>
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <div class="photo-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-search-plus fa-2x text-white"></i>
                                    </div>
                                </div>
                                
                                @if($photo->title || $photo->description)
                                    <div class="card-body p-2">
                                        @if($photo->title)
                                            <h6 class="card-title mb-1 small">{{ $photo->title }}</h6>
                                        @endif
                                        
                                        @if($photo->description)
                                            <p class="card-text small text-muted mb-0">
                                                {{ Str::limit($photo->description, 50) }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($photos->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $photos->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-images fa-4x text-muted mb-3"></i>
                    <h3 class="text-muted">Nenhuma foto encontrada</h3>
                    <p class="text-muted">Este álbum ainda não possui fotos.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Photo Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="photoModalLabel"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img id="modalPhoto" class="img-fluid" alt="">
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <p id="photoDescription" class="text-white mb-0"></p>
            </div>
        </div>
    </div>
</div>

<style>
.photo-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.photo-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.photo-overlay {
    background: rgba(0, 0, 0, 0.7);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.photo-card:hover .photo-overlay {
    opacity: 1;
}

.photo-thumbnail {
    transition: transform 0.3s ease;
}

.photo-card:hover .photo-thumbnail {
    transform: scale(1.1);
}

#photoModal .modal-content {
    background: rgba(0, 0, 0, 0.9) !important;
}

#photoModal img {
    max-height: 80vh;
    width: auto;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoModal = document.getElementById('photoModal');
    const modalPhoto = document.getElementById('modalPhoto');
    const modalTitle = document.getElementById('photoModalLabel');
    const photoDescription = document.getElementById('photoDescription');
    
    photoModal.addEventListener('show.bs.modal', function(event) {
        const trigger = event.relatedTarget;
        const photoUrl = trigger.getAttribute('data-photo-url');
        const photoTitle = trigger.getAttribute('data-photo-title');
        const photoDesc = trigger.getAttribute('data-photo-description');
        
        modalPhoto.src = photoUrl;
        modalTitle.textContent = photoTitle || '{{ $album->title }}';
        photoDescription.textContent = photoDesc || '';
        
        if (!photoDesc) {
            photoDescription.style.display = 'none';
        } else {
            photoDescription.style.display = 'block';
        }
    });
});
</script>
@endsection