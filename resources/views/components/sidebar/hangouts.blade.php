@php
$widgetConfig = App\Models\SidebarConfig::getWidgetConfig('hangouts');
@endphp

@if($hangouts && $hangouts->count() > 0)
<div class="sidebar-widget hangouts-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#ffffff' }};">
    
    <div class="widget-header" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title">
            <i class="fas fa-video me-2"></i>HANGOUTS
        </h5>
    </div>
    
    <div class="widget-content"
         style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">

        @foreach($hangouts as $hangout)
            <div class="hangout-item {{ !$loop->last ? 'border-bottom' : '' }} pb-3 mb-3">
                {{-- Header com status e plataforma --}}
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        {{-- Status Badge --}}
                        <span class="badge rounded-pill px-2 py-1 small
                            @if($hangout->status === 'live') bg-success text-white
                            @elseif($hangout->status === 'scheduled') bg-primary text-white
                            @elseif($hangout->status === 'ended') bg-secondary text-white
                            @else bg-danger text-white
                            @endif">
                            @if($hangout->status === 'live')
                                <i class="fas fa-circle me-1" style="font-size: 6px; animation: pulse 1s infinite;"></i>
                                AO VIVO
                            @else
                                {{ $hangout->status_label }}
                            @endif
                        </span>
                    </div>

                    {{-- Plataforma Icon --}}
                    <div class="d-flex align-items-center gap-1">
                        <div class="platform-icon rounded d-flex align-items-center justify-center text-white" 
                             style="width: 20px; height: 20px; background-color: {{ $hangout->platform_color }}; font-size: 10px;">
                            @if($hangout->platform === 'google-meet')
                                G
                            @elseif($hangout->platform === 'zoom')
                                Z
                            @elseif($hangout->platform === 'teams')
                                T
                            @elseif($hangout->platform === 'discord')
                                D
                            @else
                                M
                            @endif
                        </div>
                        <span class="small text-muted">{{ $hangout->platform_name }}</span>
                    </div>
                </div>

                {{-- Título --}}
                <h6 class="hangout-title fw-semibold mb-2" 
                    style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }}; font-size: 14px; line-height: 1.3;">
                    {{ $hangout->title }}
                </h6>

                {{-- Data e Duração --}}
                <div class="hangout-meta d-flex align-items-center flex-wrap gap-3 mb-2 small text-muted">
                    <div class="d-flex align-items-center gap-1">
                        <i class="fas fa-calendar-alt" style="width: 12px;"></i>
                        <span>{{ $hangout->scheduled_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="fas fa-clock" style="width: 12px;"></i>
                        <span>{{ $hangout->scheduled_at->format('H:i') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="fas fa-stopwatch" style="width: 12px;"></i>
                        <span>{{ $hangout->formatted_duration }}</span>
                    </div>
                </div>

                {{-- Anfitrião --}}
                @if($hangout->host_name)
                <div class="hangout-host d-flex align-items-center gap-1 mb-2 small text-muted">
                    <i class="fas fa-user" style="width: 12px;"></i>
                    <span>Anfitrião: <strong>{{ $hangout->host_name }}</strong></span>
                </div>
                @endif

                {{-- Descrição curta --}}
                @if($hangout->description)
                <p class="hangout-description small text-muted mb-3" style="line-height: 1.4;">
                    {{ Str::limit($hangout->description, 100) }}
                </p>
                @endif

                {{-- Countdown ou tempo até reunião --}}
                @if($hangout->isUpcoming())
                <div class="alert alert-info py-2 px-3 mb-3 small">
                    <i class="fas fa-clock me-1"></i>
                    {{ $hangout->time_until_meeting }}
                </div>
                @endif

                {{-- Ações --}}
                <div class="hangout-actions d-flex gap-2">
                    @if($hangout->canJoin())
                    <a href="{{ $hangout->meeting_link }}" 
                       target="_blank"
                       class="btn btn-success btn-sm flex-fill text-center"
                       style="font-size: 11px; padding: 6px 8px;">
                        @if($hangout->isLive())
                            <i class="fas fa-circle me-1" style="font-size: 6px; animation: pulse 1s infinite;"></i>
                            Entrar Agora
                        @else
                            <i class="fas fa-video me-1"></i>
                            Sala Aberta
                        @endif
                    </a>
                    @elseif($hangout->isUpcoming())
                    <button class="btn btn-secondary btn-sm flex-fill" 
                            style="font-size: 11px; padding: 6px 8px;" disabled>
                        <i class="fas fa-hourglass-half me-1"></i>
                        Aguardando
                    </button>
                    @else
                    <button class="btn btn-outline-secondary btn-sm flex-fill" 
                            style="font-size: 11px; padding: 6px 8px;" disabled>
                        <i class="fas fa-check-circle me-1"></i>
                        Finalizado
                    </button>
                    @endif

                    {{-- Botão de informações adicionais --}}
                    @if($hangout->meeting_id || $hangout->meeting_password)
                    <button class="btn btn-outline-secondary btn-sm" 
                            style="padding: 6px 8px; font-size: 11px;"
                            onclick="toggleMeetingInfo('{{ $hangout->id }}')"
                            title="Ver detalhes da reunião">
                        <i class="fas fa-info-circle"></i>
                    </button>
                    @endif
                </div>

                {{-- Detalhes da reunião (ocultos por padrão) --}}
                @if($hangout->meeting_id || $hangout->meeting_password)
                <div id="meeting-info-{{ $hangout->id }}" class="meeting-details mt-3 p-2 bg-light rounded small" style="display: none;">
                    @if($hangout->meeting_id)
                    <div class="mb-2">
                        <strong>ID da Reunião:</strong>
                        <code class="bg-white px-2 py-1 rounded border ms-1">{{ $hangout->meeting_id }}</code>
                    </div>
                    @endif
                    @if($hangout->meeting_password)
                    <div>
                        <strong>Senha:</strong>
                        <code class="bg-white px-2 py-1 rounded border ms-1">{{ $hangout->meeting_password }}</code>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        @endforeach

    </div>
    
    @if($widgetConfig?->custom_css)
        <style>
            {!! $widgetConfig->custom_css !!}
        </style>
    @endif
</div>

<style>
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.hangouts-widget .hangout-item {
    transition: all 0.2s ease;
}

.hangouts-widget .hangout-item:hover {
    transform: translateX(2px);
}

.hangouts-widget .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.hangouts-widget .platform-icon {
    font-weight: bold;
    font-size: 10px !important;
}

.hangouts-widget .hangout-title {
    font-weight: 600;
}
</style>

<script>
function toggleMeetingInfo(hangoutId) {
    const element = document.getElementById('meeting-info-' + hangoutId);
    if (element.style.display === 'none') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}
</script>
@else
<div class="sidebar-widget hangouts-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#ffffff' }};">
    
    <div class="widget-header" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title">
            <i class="fas fa-video me-2"></i>HANGOUTS
        </h5>
    </div>
    
    <div class="widget-content text-center">
        <div class="no-hangouts-message">
            <div class="no-hangouts-icon text-muted mb-2">
                <i class="fas fa-video" style="font-size: 2.5rem; opacity: 0.3;"></i>
            </div>
            <p class="text-muted small mb-0">
                Nenhum hangout agendado no momento
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