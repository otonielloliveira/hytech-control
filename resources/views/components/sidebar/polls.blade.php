@php
$activePoll = App\Models\Poll::active()->byPriority()->first();
$widgetConfig = App\Models\SidebarConfig::getWidgetConfig('polls');
$hasVoted = false;

if ($activePoll && request()->ip()) {
    $hasVoted = App\Models\PollVote::hasVotedInPoll($activePoll->id, request()->ip());
}
@endphp

@if($activePoll)
<div class="sidebar-widget polls-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#ffffff' }};">
    
    <div class="widget-header" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title">
            <i class="fas fa-chart-bar me-2"></i>ENQUETES
        </h5>
    </div>
    
    <div class="widget-content">
        <div class="poll-container">
            <h6 class="poll-title mb-3" 
                style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                {{ $activePoll->title }}
            </h6>
            
            @if($activePoll->description)
                <p class="poll-description text-muted small mb-3">
                    {{ $activePoll->description }}
                </p>
            @endif
            
            @if($hasVoted)
                {{-- Mostrar resultados --}}
                <div class="poll-results">
                    <div class="alert alert-success py-2 px-3 mb-3 small">
                        <i class="fas fa-check-circle me-1"></i>
                        Você já votou nesta enquete
                    </div>
                    
                    @foreach($activePoll->options()->byPriority()->get() as $option)
                        <div class="poll-option-result mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="option-text small fw-medium" 
                                      style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                                    {{ $option->option_text }}
                                </span>
                                <span class="option-percentage small fw-bold" 
                                      style="color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
                                    {{ $option->vote_percentage }}%
                                </span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" 
                                     style="width: {{ $option->vote_percentage }}%; background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};"
                                     role="progressbar"></div>
                            </div>
                            <div class="option-votes text-muted small mt-1">
                                {{ $option->votes_count }} {{ $option->votes_count === 1 ? 'voto' : 'votos' }}
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="poll-stats text-center pt-3 mt-3 border-top">
                        <div class="total-votes text-muted small">
                            <i class="fas fa-users me-1"></i>
                            Total: {{ $activePoll->total_votes }} {{ $activePoll->total_votes === 1 ? 'voto' : 'votos' }}
                            @if($activePoll->expires_at)
                                <br><i class="fas fa-clock me-1"></i>
                                Expira em: {{ $activePoll->expires_at->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>
                </div>
            @else
                {{-- Formulário de votação --}}
                <form id="poll-form" action="{{ route('polls.vote', $activePoll->id) }}" method="POST" class="poll-voting">
                    @csrf
                    <div class="poll-options mb-3">
                        @foreach($activePoll->options()->byPriority()->get() as $option)
                            <label class="poll-option-label d-flex align-items-center p-2 rounded mb-2" 
                                   style="border: 1px solid #e5e7eb; cursor: pointer; transition: all 0.2s ease;">
                                <input type="radio" 
                                       name="option_id" 
                                       value="{{ $option->id }}" 
                                       class="form-check-input me-3"
                                       style="accent-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};"
                                       required>
                                <span class="option-text small" 
                                      style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                                    {{ $option->option_text }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    
                    <div class="poll-actions">
                        <button type="submit" 
                                class="btn btn-sm w-100 poll-vote-btn"
                                style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }}; border-color: {{ $widgetConfig?->title_color ?? '#1e40af' }}; color: white;">
                            <i class="fas fa-vote-yea me-2"></i>VOTAR
                        </button>
                        
                        @if($activePoll->expires_at)
                            <div class="poll-expires text-center text-muted small mt-2">
                                <i class="fas fa-clock me-1"></i>
                                Expira em: {{ $activePoll->expires_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    </div>
                </form>
            @endif
        </div>
    </div>
    
    @if($widgetConfig?->custom_css)
        <style>
            {!! $widgetConfig->custom_css !!}
        </style>
    @endif
</div>

<style>
.polls-widget .poll-option-label:hover {
    background-color: rgba(59, 130, 246, 0.05);
    border-color: {{ $widgetConfig?->title_color ?? '#1e40af' }} !important;
    transform: translateX(2px);
}

.polls-widget .poll-vote-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.polls-widget .poll-title {
    font-size: 15px;
    font-weight: 600;
    line-height: 1.4;
}

.polls-widget .progress {
    border-radius: 4px;
    background-color: #e5e7eb;
    overflow: hidden;
}

.polls-widget .progress-bar {
    border-radius: 4px;
    transition: width 0.3s ease;
}
</style>

@if($activePoll && !$hasVoted)
<script>
document.getElementById('poll-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const selectedOption = formData.get('option_id');
    const submitBtn = this.querySelector('.poll-vote-btn');
    
    if (!selectedOption) {
        alert('Por favor, selecione uma opção antes de votar.');
        return;
    }
    
    // Desabilitar botão durante o envio
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>VOTANDO...';
    
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            option_id: selectedOption
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recarregar a página para mostrar os resultados
            window.location.reload();
        } else {
            alert(data.message || 'Erro ao votar. Tente novamente.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-vote-yea me-2"></i>VOTAR';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao votar. Tente novamente.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-vote-yea me-2"></i>VOTAR';
    });
});
</script>
@endif
@else
<div class="sidebar-widget polls-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#ffffff' }};">
    
    <div class="widget-header" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title">
            <i class="fas fa-chart-bar me-2"></i>ENQUETES
        </h5>
    </div>
    
    <div class="widget-content text-center">
        <div class="no-poll-message">
            <div class="no-poll-icon text-muted mb-2">
                <i class="fas fa-chart-bar" style="font-size: 2.5rem; opacity: 0.3;"></i>
            </div>
            <p class="text-muted small mb-0">
                Nenhuma enquete ativa no momento
            </p>
        </div>
    </div>
    
    @if($widgetConfig?->custom_css)
        <style>
            {!! $widgetConfig->custom_css !!}
        </style>
    @endif
</div>
@endif