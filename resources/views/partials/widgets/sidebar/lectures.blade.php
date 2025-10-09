<div class="lectures-widget">
    @php
        $lectures = collect([]);
        $error = false;
        
        try {
            // Buscar próximas palestras
            $lectures = \App\Models\Lecture::upcoming(3);
        } catch (\Exception $e) {
            $error = true;
        }
    @endphp
    
    @if(!$error && $lectures->count() > 0)
        <div class="lectures-list">
            @foreach($lectures as $lecture)
                <div class="lecture-item mb-3 pb-2 border-bottom">
                    <h6 class="mb-1">
                        <a href="{{ route('lectures.show', $lecture) }}" 
                           class="text-decoration-none">
                            {{ Str::limit($lecture->title, 40) }}
                        </a>
                    </h6>
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $lecture->formatted_start_date }}
                    </small>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-3">
            <a href="{{ route('lectures.index') }}" class="btn btn-primary btn-sm">
                Ver todas as palestras
            </a>
        </div>
    @elseif(!$error)
        <div class="text-center">
            <i class="fas fa-microphone fa-2x text-muted mb-2"></i>
            <p class="text-muted small">Nenhuma palestra agendada no momento.</p>
            <a href="{{ route('lectures.index') }}" class="btn btn-outline-primary btn-sm">
                Ver palestras
            </a>
        </div>
    @else
        <div class="text-center">
            <i class="fas fa-microphone fa-2x text-muted mb-2"></i>
            <p class="text-muted small">Sistema de palestras em desenvolvimento...</p>
            <a href="{{ route('lectures.index') }}" class="btn btn-outline-primary btn-sm">
                Acessar palestras
            </a>
        </div>
    @endif
</div>