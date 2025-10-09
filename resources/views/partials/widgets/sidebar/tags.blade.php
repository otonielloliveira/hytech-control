<div class="tags-widget">
    @php
        $tags = collect([]);
        $error = false;
        
        try {
            // Buscar tags populares com fallback para tags normais
            $tags = \App\Models\Tag::popular(10);
            if ($tags->isEmpty()) {
                $tags = \App\Models\Tag::take(10)->get();
            }
        } catch (\Exception $e) {
            $error = true;
            $errorMessage = config('app.debug') ? $e->getMessage() : '';
        }
    @endphp
    
    @if(!$error && $tags->count() > 0)
        <div class="tag-cloud">
            @foreach($tags as $tag)
                <a href="{{ route('blog.tag.show', $tag->slug) }}" 
                   class="tag-link me-1 mb-1 d-inline-block">
                    <span class="badge bg-secondary">{{ $tag->name }}</span>
                </a>
            @endforeach
        </div>
    @elseif(!$error)
        <div class="text-center">
            <i class="fas fa-tags fa-2x text-muted mb-2"></i>
            <p class="text-muted small">Nenhuma tag encontrada.</p>
            <small class="text-muted">Tags aparecerão aqui conforme posts forem criados.</small>
        </div>
    @else
        <div class="text-center">
            <i class="fas fa-tags fa-2x text-muted mb-2"></i>
            <p class="text-muted small">Sistema de tags temporariamente indisponível.</p>
            @if(config('app.debug') && isset($errorMessage))
                <small class="text-muted">{{ $errorMessage }}</small>
            @endif
        </div>
    @endif
</div>

<style>
.tag-link:hover .badge {
    background-color: var(--primary-color, #007bff) !important;
    color: white;
    transform: scale(1.05);
    transition: all 0.3s ease;
}

.tag-cloud .badge {
    transition: all 0.3s ease;
}
</style>