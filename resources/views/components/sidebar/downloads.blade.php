@php
$downloads = App\Models\Download::getActiveDownloads(5);
$widgetConfig = App\Models\SidebarConfig::getWidgetConfig('downloads');
@endphp

@if($downloads->count() > 0)
<div class="sidebar-widget downloads-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#ffffff' }};">
    
    <div class="widget-header" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title">
            <i class="fas fa-download me-2"></i>{{ $widgetConfig?->display_name ?? 'DOWNLOADS' }}
        </h5>
    </div>
    
    <div class="widget-content">
        @foreach($downloads as $download)
            <div class="download-item @if(!$loop->last) border-bottom @endif">
                
                <div class="download-content">
                    <div class="d-flex align-items-start">
                        <div class="download-icon me-3">
                            <i class="{{ $download->icon_class }} fa-2x" 
                               style="color: {{ $widgetConfig?->title_color ?? '#1e40af' }};"></i>
                        </div>
                        
                        <div class="download-info flex-grow-1">
                            <h6 class="download-title mb-1" 
                                style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                                {{ $download->title }}
                            </h6>
                            
                            @if($download->description)
                                <p class="download-description text-muted small mb-2">
                                    {{ Str::limit($download->description, 80) }}
                                </p>
                            @endif
                            
                            <div class="download-meta small">
                                <div class="row">
                                    <div class="col-6">
                                        <span class="text-muted">
                                            <i class="fas fa-file me-1"></i>
                                            {{ strtoupper($download->file_type) }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted">
                                            <i class="fas fa-weight-hanging me-1"></i>
                                            {{ $download->formatted_file_size }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if($download->category)
                                    <div class="download-category mt-1">
                                        <span class="badge badge-sm" 
                                              style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }}; color: white;">
                                            {{ $download->category }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="download-actions mt-2">
                                <a href="{{ route('downloads.download', $download->id) }}" 
                                   class="btn btn-sm download-btn w-100"
                                   style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }}; border-color: {{ $widgetConfig?->title_color ?? '#1e40af' }}; color: white;"
                                   @if($download->requires_login && !Auth::check()) 
                                       onclick="alert('É necessário fazer login para baixar este arquivo.'); return false;"
                                   @endif>
                                    <i class="fas fa-download me-2"></i>
                                    BAIXAR
                                    @if($download->download_count > 0)
                                        <span class="badge bg-light text-dark ms-2">
                                            {{ $download->download_count }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        @if(\App\Models\Download::active()->count() > 5)
            <div class="downloads-footer text-center mt-3 pt-2 border-top">
                <a href="{{ route('downloads.index') }}" 
                   class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-list me-2"></i>
                    Ver Todos os Downloads
                </a>
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
.downloads-widget .download-item {
    padding: 15px 0;
}

.downloads-widget .download-item:last-child {
    border-bottom: none !important;
    padding-bottom: 0;
}

.downloads-widget .download-item:first-child {
    padding-top: 0;
}

.downloads-widget .download-icon {
    width: 40px;
    text-align: center;
}

.downloads-widget .download-title {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.3;
}

.downloads-widget .download-description {
    font-size: 12px;
    line-height: 1.4;
}

.downloads-widget .download-meta {
    font-size: 11px;
}

.downloads-widget .download-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.downloads-widget .download-item:hover {
    background-color: rgba(0,0,0,0.02);
    margin: 0 -20px;
    padding-left: 20px;
    padding-right: 20px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.downloads-widget .badge {
    font-size: 10px;
    padding: 2px 6px;
}
</style>
@else
<div class="sidebar-widget downloads-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#ffffff' }};">
    
    <div class="widget-header" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title">
            <i class="fas fa-download me-2"></i>{{ $widgetConfig?->display_name ?? 'DOWNLOADS' }}
        </h5>
    </div>
    
    <div class="widget-content text-center">
        <div class="no-downloads-message">
            <div class="no-downloads-icon text-muted mb-2">
                <i class="fas fa-download" style="font-size: 2.5rem; opacity: 0.3;"></i>
            </div>
            <p class="text-muted small mb-0">
                Nenhum arquivo disponível para download no momento
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