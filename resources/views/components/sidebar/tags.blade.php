@php
$tags = App\Models\Tag::withCount([
    'posts' => function ($query) {
        $query->where('status', 'published')
              ->where('published_at', '<=', now());
    }
])
->orderBy('posts_count', 'desc')
->take(10)
->get();

$widgetConfig = App\Models\SidebarConfig::getWidgetConfig('tags');
@endphp

@if($tags->count() > 0)
<div class="sidebar-widget tags-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#ffffff' }};">
    
    <div class="widget-header" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title">
            <i class="fas fa-tags me-2"></i>TAGS
        </h5>
    </div>
    
    <div class="widget-content">
        <div class="tags-cloud">
            @foreach($tags as $tag)
                <a href="{{ route('blog.tag.show', $tag->slug) }}" 
                   class="tag-link"
                   style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
                    {{ $tag->name }} ({{ $tag->posts_count }})
                </a>
            @endforeach
        </div>
    </div>
    
    @if($widgetConfig?->custom_css)
        <style>
            {!! $widgetConfig->custom_css !!}
        </style>
    @endif
</div>
@endif