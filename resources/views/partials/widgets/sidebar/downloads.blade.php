<div class="downloads-widget">
    @php
        $downloads = collect([]);
        $error = false;
        
        try {
            // Buscar downloads populares
            $downloads = \App\Models\Download::popular(3);
        } catch (\Exception $e) {
            $error = true;
        }
    @endphp
    
    @if(!$error && $downloads->count() > 0)
        <div class="downloads-list">
            @foreach($downloads as $download)
                <div class="download-item mb-3 pb-2 border-bottom">
                    <h6 class="mb-1">
                        <a href="{{ route('downloads.show', $download) }}" 
                           class="text-decoration-none">
                            {{ Str::limit($download->title, 40) }}
                        </a>
                    </h6>
                    <small class="text-muted">
                        <i class="fas fa-download me-1"></i>
                        {{ $download->download_count }} downloads
                    </small>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-3">
            <a href="{{ route('downloads.index') }}" class="btn btn-primary btn-sm">
                Ver todos os downloads
            </a>
        </div>
    @elseif(!$error)
        <div class="text-center">
            <i class="fas fa-download fa-2x text-muted mb-2"></i>
            <p class="text-muted small">Nenhum download disponível no momento.</p>
            <a href="{{ route('downloads.index') }}" class="btn btn-outline-primary btn-sm">
                Ver downloads
            </a>
        </div>
    @else
        <div class="text-center">
            <i class="fas fa-download fa-2x text-muted mb-2"></i>
            <p class="text-muted small">Sistema de downloads em desenvolvimento...</p>
            <a href="{{ route('downloads.index') }}" class="btn btn-outline-primary btn-sm">
                Acessar downloads
            </a>
        </div>
    @endif
</div>