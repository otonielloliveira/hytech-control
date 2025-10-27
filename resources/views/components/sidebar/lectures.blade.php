@php
$lectures = App\Models\Lecture::getActiveLectures(3);
$widgetConfig = App\Models\SidebarConfig::getWidgetConfig('lectures');
@endphp

@if($lectures->count() > 0)
<div class="sidebar-widget lectures-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#ffffff' }};">
    
    <div class="widget-header" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title">
            <i class="fas fa-microphone me-2"></i>{{ $widgetConfig?->display_name ?? 'PALESTRAS' }}
        </h5>
    </div>
    
    <div class="widget-content">
        @foreach($lectures as $lecture)
            <div class="lecture-item @if(!$loop->last) border-bottom @endif">
                <div class="lecture-content">
                    <h6 class="lecture-title" 
                        style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                        @if($lecture->link_url)
                            <a href="{{ $lecture->link_url }}" 
                               class="text-decoration-none lecture-link"
                               style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};"
                               target="_blank">
                                {{ $lecture->title }}
                                <i class="fas fa-external-link-alt ms-1 small"></i>
                            </a>
                        @else
                            {{ $lecture->title }}
                        @endif
                    </h6>
                    
                    @if($lecture->speaker)
                        <div class="lecture-speaker" 
                             style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                            <i class="fas fa-user-tie me-1"></i>
                            {{ $lecture->speaker }}
                        </div>
                    @endif
                    
                    @if($lecture->date_time)
                        <div class="lecture-date" 
                             style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $lecture->date_time->format('d/m/Y H:i') }}
                        </div>
                    @endif
                    
                    @if($lecture->location)
                        <div class="lecture-location" 
                             style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $lecture->location }}
                        </div>
                    @endif
                    
                    @if($lecture->description)
                        <div class="lecture-description" 
                             style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">
                            {!! Str::limit(strip_tags($lecture->description), 80) !!}
                        </div>
                    @endif
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