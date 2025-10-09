<div class="bg-white rounded-lg shadow-md p-4 mb-6 border border-gray-200 hover:shadow-lg transition-shadow duration-300">
    @php
        $titleColor = $widget['config']->title_color ?? '#1e40af';
        $bgColor = $widget['config']->background_color ?? '#f8fafc';
        $textColor = $widget['config']->text_color ?? '#1f2937';
        
        $activePoll = \App\Models\Poll::active()->byPriority()->first();
        $hasVoted = false;
        
        if ($activePoll && request()->ip()) {
            $hasVoted = \App\Models\PollVote::hasVotedInPoll($activePoll->id, request()->ip());
        }
    @endphp
    
    <style>
        .widget-polls {
            background-color: {{ $bgColor }};
            color: {{ $textColor }};
        }
        .widget-polls h3 {
            color: {{ $titleColor }};
        }
    </style>
    
    <div class="widget-polls">
        <h3 class="text-lg font-bold mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $widget['title'] ?? 'Enquetes' }}
        </h3>
        
        @if($activePoll)
            <div class="poll-container">
                <h4 class="font-semibold text-base mb-2">{{ $activePoll->title }}</h4>
                
                @if($activePoll->description)
                    <p class="text-sm text-gray-600 mb-3">{{ $activePoll->description }}</p>
                @endif
                
                @if($hasVoted)
                    {{-- Mostrar resultados --}}
                    <div class="poll-results">
                        <p class="text-sm text-green-600 mb-3 font-medium">✓ Você já votou nesta enquete</p>
                        
                        @foreach($activePoll->options()->byPriority()->get() as $option)
                            <div class="mb-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>{{ $option->option_text }}</span>
                                    <span class="font-medium">{{ $option->vote_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                         style="width: {{ $option->vote_percentage }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ $option->votes_count }} votos</div>
                            </div>
                        @endforeach
                        
                        <div class="text-sm text-gray-500 text-center mt-3 pt-2 border-t">
                            Total: {{ $activePoll->total_votes }} votos
                            @if($activePoll->expires_at)
                                <br>Expira em: {{ $activePoll->expires_at->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Formulário de votação --}}
                    <form id="poll-form" action="{{ route('polls.vote', $activePoll->id) }}" method="POST" class="poll-voting">
                        @csrf
                        <div class="space-y-2 mb-4">
                            @foreach($activePoll->options()->byPriority()->get() as $option)
                                <label class="flex items-center space-x-2 p-2 rounded hover:bg-gray-50 cursor-pointer transition-colors">
                                    <input type="radio" name="option_id" value="{{ $option->id }}" 
                                           class="text-blue-600 focus:ring-blue-500" required>
                                    <span class="text-sm">{{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition-colors duration-200 font-medium text-sm">
                            Votar
                        </button>
                        
                        @if($activePoll->expires_at)
                            <div class="text-xs text-gray-500 text-center mt-2">
                                Expira em: {{ $activePoll->expires_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    </form>
                @endif
            </div>
        @else
            <div class="text-center text-gray-500 py-4">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm">Nenhuma enquete ativa no momento</p>
            </div>
        @endif
    </div>
</div>

@if($activePoll && !$hasVoted)
<script>
document.getElementById('poll-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const selectedOption = formData.get('option_id');
    
    if (!selectedOption) {
        alert('Por favor, selecione uma opção antes de votar.');
        return;
    }
    
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
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao votar. Tente novamente.');
    });
});
</script>
@endif