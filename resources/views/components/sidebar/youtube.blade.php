@php
$config = App\Models\BlogConfig::current();
$widgetConfig = App\Models\SidebarConfig::getWidgetConfig('youtube');
$youtubeService = app(App\Services\YouTubeService::class);
$channelData = $youtubeService->getConfigChannelData();
$recentVideos = [];

// Buscar vídeos recentes se tiver channel ID
if ($config->youtube_channel_id) {
    $recentVideos = $youtubeService->getChannelVideos($config->youtube_channel_id, 3);
}
@endphp

@if($config->show_youtube_widget && $config->youtube_channel_url)
<div class="sidebar-widget youtube-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#ffffff' }};">
    
    <div class="widget-header" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title">
            <i class="fab fa-youtube me-2"></i>CANAL NO YOUTUBE
        </h5>
    </div>
    
    <div class="widget-content">
        <div class="youtube-info mb-3 text-center">
            @if(isset($channelData['thumbnail']))
                <div class="channel-avatar mb-3">
                    <img src="{{ $channelData['thumbnail'] }}" 
                         alt="{{ $channelData['title'] }}"
                         class="rounded-circle"
                         style="width: 80px; height: 80px; object-fit: cover;">
                </div>
            @else
                <div class="youtube-icon mb-3">
                    <i class="fab fa-youtube text-danger" style="font-size: 4rem;"></i>
                </div>
            @endif
            
            <h6 class="channel-name mb-2 fw-bold" 
                style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                {{ $channelData['title'] ?? $config->youtube_channel_name ?? 'Nosso Canal' }}
            </h6>
            
            @if(isset($channelData['description']))
                <p class="channel-description text-muted small mb-3" style="line-height: 1.4;">
                    {{ Str::limit($channelData['description'], 100) }}
                </p>
            @endif
            
            <div class="youtube-stats mb-3">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="stat-item">
                            <div class="stat-icon text-danger mb-1">
                                <i class="fas fa-play-circle"></i>
                            </div>
                            <div class="stat-number small fw-bold">
                                {{ $channelData['video_count'] ?? '0' }}
                            </div>
                            <div class="stat-label small text-muted">Vídeos</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-item">
                            <div class="stat-icon text-danger mb-1">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number small fw-bold">
                                {{ $channelData['subscriber_count'] ?? '0' }}
                            </div>
                            <div class="stat-label small text-muted">Inscritos</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-item">
                            <div class="stat-icon text-danger mb-1">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-number small fw-bold">
                                {{ $channelData['view_count'] ?? '0' }}
                            </div>
                            <div class="stat-label small text-muted">Visualizações</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if(count($recentVideos) > 0)
            <div class="recent-videos mb-3">
                <h6 class="small fw-bold mb-2 text-muted">VÍDEOS RECENTES</h6>
                @foreach($recentVideos as $video)
                    <div class="video-item mb-2 p-2 rounded border" style="border-color: #e5e7eb;">
                        <div class="row align-items-center">
                            <div class="col-4">
                                <img src="{{ $video['thumbnail'] }}" 
                                     alt="{{ $video['title'] }}"
                                     class="img-fluid rounded"
                                     style="width: 100%; height: 40px; object-fit: cover;">
                            </div>
                            <div class="col-8">
                                <a href="{{ $video['url'] }}" 
                                   target="_blank" 
                                   class="text-decoration-none small fw-medium"
                                   style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }}; line-height: 1.2;">
                                    {{ Str::limit($video['title'], 45) }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        <div class="youtube-actions">
            <a href="{{ $config->youtube_channel_url }}" 
               target="_blank" 
               class="btn btn-danger btn-sm w-100 mb-2">
                <i class="fab fa-youtube me-2"></i>VISITAR CANAL
            </a>
            
            @if($config->youtube_channel_id)
                <a href="https://www.youtube.com/channel/{{ $config->youtube_channel_id }}/videos" 
                   target="_blank" 
                   class="btn btn-outline-danger btn-sm w-100">
                    <i class="fas fa-play me-2"></i>Ver Todos os Vídeos
                </a>
            @endif
        </div>
        
        @if($config->youtube_data_last_update)
            <div class="update-info text-center mt-3 pt-2 border-top">
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Atualizado: {{ $config->youtube_data_last_update->diffForHumans() }}
                </small>
            </div>
        @endif
    </div>
    
    @if($widgetConfig?->custom_css)
        <style>
            {!! $widgetConfig->custom_css !!}
        </style>
    @endif
</div>

<style>
.youtube-widget .stat-item {
    transition: transform 0.2s ease;
}

.youtube-widget .stat-item:hover {
    transform: scale(1.1);
}

.youtube-widget .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.youtube-widget .video-item:hover {
    background-color: rgba(0,0,0,0.02);
    transform: translateX(2px);
    transition: all 0.2s ease;
}

.youtube-widget .channel-avatar img {
    border: 3px solid #ffffff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.youtube-widget .video-item a:hover {
    color: #dc2626 !important;
}
</style>
@endif