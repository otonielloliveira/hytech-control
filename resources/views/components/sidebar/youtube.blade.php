@php
$config = App\Models\BlogConfig::current();
$widgetConfig = App\Models\SidebarConfig::getWidgetConfig('youtube');
@endphp

@if($config->show_youtube_widget && $config->youtube_channel_url)
<div class="sidebar-widget youtube-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#f8fafc' }};">
    
    <div class="widget-header p-3" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title mb-0 text-white">
            <i class="fab fa-youtube me-2"></i>Canal no Youtube
        </h5>
    </div>
    
    <div class="widget-content p-3 text-center">
        <div class="youtube-info mb-3">
            <div class="youtube-icon mb-2">
                <i class="fab fa-youtube text-danger" style="font-size: 3rem;"></i>
            </div>
            
            @if($config->youtube_channel_name)
                <h6 class="channel-name mb-2" 
                    style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                    {{ $config->youtube_channel_name }}
                </h6>
            @endif
            
            <div class="youtube-stats mb-3">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="stat-item">
                            <div class="stat-icon text-danger mb-1">
                                <i class="fas fa-play-circle"></i>
                            </div>
                            <div class="stat-number small fw-bold">72</div>
                            <div class="stat-label small text-muted">Vídeos</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-item">
                            <div class="stat-icon text-danger mb-1">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number small fw-bold">243</div>
                            <div class="stat-label small text-muted">Inscritos</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-item">
                            <div class="stat-icon text-danger mb-1">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-number small fw-bold">11380</div>
                            <div class="stat-label small text-muted">Visitantes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="youtube-actions">
            <a href="{{ $config->youtube_channel_url }}" 
               target="_blank" 
               class="btn btn-danger btn-sm w-100 mb-2">
                <i class="fab fa-youtube me-2"></i>CLIQUE AQUI
            </a>
            
            <a href="{{ $config->youtube_channel_url }}" 
               target="_blank" 
               class="btn btn-outline-danger btn-sm w-100">
                <i class="fas fa-play me-2"></i>Galeria de Vídeo
            </a>
        </div>
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
</style>
@endif