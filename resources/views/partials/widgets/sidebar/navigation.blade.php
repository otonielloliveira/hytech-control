<div class="navigation-widget">
    <div class="list-group">
        <a href="{{ route('albums.index') }}" class="list-group-item list-group-item-action border-0 py-2">
            <i class="fas fa-images text-primary me-2"></i>
            Álbuns de Fotos
        </a>
        <a href="{{ route('videos.index') }}" class="list-group-item list-group-item-action border-0 py-2">
            <i class="fas fa-video text-danger me-2"></i>
            Vídeos
        </a>
        <a href="{{ route('downloads.index') }}" class="list-group-item list-group-item-action border-0 py-2">
            <i class="fas fa-download text-success me-2"></i>
            Downloads
        </a>
        <a href="{{ route('lectures.index') }}" class="list-group-item list-group-item-action border-0 py-2">
            <i class="fas fa-microphone text-info me-2"></i>
            Palestras
        </a>
    </div>
</div>

<style>
.navigation-widget .list-group-item:hover {
    background-color: var(--bs-light);
    border-left: 3px solid var(--bs-primary);
}
</style>