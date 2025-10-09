@php
$notices = App\Models\Notice::getActiveNotices(4);
$widgetConfig = App\Models\SidebarConfig::getWidgetConfig('notices');
@endphp

@if($notices->count() > 0)
<div class="sidebar-widget notices-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#ffffff' }};">
    
    <div class="widget-header" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title">
            <i class="fas fa-bullhorn me-2"></i>RECADOS
        </h5>
    </div>
    
    <div class="widget-content">
        @foreach($notices as $notice)
            <div class="notice-item @if(!$loop->last) border-bottom @endif">
                
                @if($notice->image)
                    <div class="notice-image">
                        <img src="{{ $notice->image_url }}" 
                             alt="{{ $notice->title }}" 
                             class="img-fluid">
                    </div>
                @endif
                
                <div class="notice-content">
                    <h6 class="notice-title" 
                        style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                        @if($notice->hasValidLink())
                            <a href="{{ $notice->final_link }}" 
                               class="text-decoration-none notice-link"
                               style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};"
                               @if($notice->link_type === 'external') target="_blank" @endif>
                                {{ $notice->title }}
                                @if($notice->link_type === 'external')
                                    <i class="fas fa-external-link-alt ms-1 small"></i>
                                @endif
                            </a>
                        @else
                            {{ $notice->title }}
                        @endif
                    </h6>
                    
                    <div class="notice-text" 
                         style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                        {!! Str::limit(strip_tags($notice->content), 120) !!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    @if($widgetConfig?->custom_css)
        <style>
            {!! $widgetConfig->custom_css !!}
        </style>
    @endif
</div>
@endif