<div class="quick-links-widget">
    <div class="row g-2">
        <div class="col-6">
            <a href="{{ route('albums.index') }}" class="btn btn-outline-primary btn-sm w-100">
                <i class="fas fa-images mb-1 d-block"></i>
                <small>Álbuns</small>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('videos.index') }}" class="btn btn-outline-danger btn-sm w-100">
                <i class="fas fa-video mb-1 d-block"></i>
                <small>Vídeos</small>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('downloads.index') }}" class="btn btn-outline-success btn-sm w-100">
                <i class="fas fa-download mb-1 d-block"></i>
                <small>Downloads</small>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('lectures.index') }}" class="btn btn-outline-info btn-sm w-100">
                <i class="fas fa-microphone mb-1 d-block"></i>
                <small>Palestras</small>
            </a>
        </div>
    </div>
</div>

<style>
.quick-links-widget .btn {
    border-radius: 8px;
    padding: 0.75rem 0.5rem;
    text-decoration: none;
    transition: all 0.3s ease;
    min-height: 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.quick-links-widget .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.quick-links-widget .btn i {
    font-size: 1.2rem;
}

.quick-links-widget .btn small {
    font-size: 0.75rem;
    font-weight: 500;
}
</style>