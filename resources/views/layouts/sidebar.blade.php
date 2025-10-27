@php
use App\Services\SidebarService;

$sidebarConfig = SidebarService::getSidebarConfig();
$activeWidgets = SidebarService::getActiveWidgets();
@endphp

@if($sidebarConfig['show_sidebar'] && count($activeWidgets) > 0)
<div class="blog-sidebar" style="width: {{ $sidebarConfig['width'] }};">
    <div class="sidebar-content">
        @foreach($activeWidgets as $widget)
            @includeIf('components.sidebar.' . $widget['name'])
        @endforeach
    </div>
</div>

<style>
.blog-sidebar {
    flex-shrink: 0;
    padding: 0 0px;
}

.sidebar-widget {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.sidebar-widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.widget-header {
    padding: 16px 20px;
    position: relative;
}

.widget-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
}

.widget-title {
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 0;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.widget-content {
    padding: 20px;
    line-height: 1.6;
}

/* Notice Widget Specific */
.notice-item {
    padding: 15px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.notice-item:last-child {
    border-bottom: none !important;
    padding-bottom: 0;
}

.notice-item:first-child {
    padding-top: 0;
}

.notice-image {
    margin-bottom: 12px;
}

.notice-image img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
}

.notice-title {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 8px;
    line-height: 1.4;
}

.notice-link:hover {
    text-decoration: underline !important;
}

.notice-text {
    font-size: 13px;
    line-height: 1.5;
    opacity: 0.85;
}

/* Tags Widget Specific */
.tags-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tag-link {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    color: white !important;
    text-decoration: none;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.tag-link:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    opacity: 0.9;
}

/* YouTube Widget Specific */
.youtube-widget .btn {
    border-radius: 8px;
    font-weight: 600;
    padding: 10px;
    transition: all 0.3s ease;
}

.youtube-widget .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.stat-item {
    transition: transform 0.2s ease;
}

.stat-item:hover {
    transform: scale(1.05);
}

.stat-number {
    font-weight: 700;
    font-size: 16px;
}

@media (max-width: 768px) {
    .blog-sidebar {
        width: 100% !important;
        margin-top: 2rem;
        padding: 0;
    }
    
    .widget-content {
        padding: 15px;
    }
    
    .widget-header {
        padding: 12px 15px;
    }
}
</style>
@endif