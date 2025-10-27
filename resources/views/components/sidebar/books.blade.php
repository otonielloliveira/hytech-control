@php
$widgetConfig = App\Models\SidebarConfig::getWidgetConfig('books');
@endphp

@if($books && $books->count() > 0)
<div class="sidebar-widget books-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#ffffff' }};">
    
    <div class="widget-header" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title">
            <i class="fas fa-book me-2"></i>{{ $widgetConfig?->display_name ?? 'LIVROS RECOMENDADOS' }}
        </h5>
    </div>
    
    <div class="widget-content"
         style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }};">

        @foreach($books as $book)
            <div class="book-item {{ !$loop->last ? 'border-bottom' : '' }} pb-3 mb-3">
                <div class="d-flex gap-3">
                    {{-- Capa do Livro --}}
                    <div class="flex-shrink-0">
                        <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default-no-image.png') }}" 
                             alt="{{ $book->title }}"
                             class="book-cover img-fluid rounded shadow-sm border" 
                             style="width: 60px; height: 75px; object-fit: cover;">
                    </div>

                    {{-- Informações do Livro --}}
                    <div class="flex-fill">
                        <h6 class="book-title fw-semibold mb-1" 
                            style="color: {{ $widgetConfig?->text_color ?? '#1f2937' }}; font-size: 14px; line-height: 1.3;">
                            {{ $book->title }}
                        </h6>
                        
                        <p class="book-author small text-muted mb-1">
                            por <span class="fw-medium">{{ $book->author }}</span>
                        </p>

                        {{-- Avaliação --}}
                        @if($book->rating)
                        <div class="book-rating d-flex align-items-center gap-1 mb-2">
                            <div class="stars small text-warning">
                                {{ $book->rating_stars }}
                            </div>
                            <span class="rating-text small text-muted">{{ $book->formatted_rating }}</span>
                        </div>
                        @endif

                        {{-- Categoria --}}
                        @if($book->category)
                        <div class="book-category mb-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary small px-2 py-1">
                                {{ ucfirst(str_replace('-', ' ', $book->category)) }}
                            </span>
                        </div>
                        @endif

                        {{-- Descrição curta --}}
                        @if($book->short_description)
                        <p class="book-description small text-muted mb-3" style="line-height: 1.4;">
                            {{ $book->short_description }}
                        </p>
                        @endif

                        {{-- Links de ação --}}
                        <div class="book-actions d-flex gap-1 flex-wrap">
                            @if($book->hasAmazonLink())
                            <a href="{{ $book->amazon_link }}" 
                               target="_blank"
                               class="btn btn-warning btn-sm text-white" 
                               style="font-size: 10px; padding: 4px 8px;"
                               title="Ver na Amazon">
                                <i class="fab fa-amazon me-1"></i>Amazon
                            </a>
                            @endif

                            @if($book->hasGoodreadsLink())
                            <a href="{{ $book->goodreads_link }}" 
                               target="_blank"
                               class="btn btn-info btn-sm text-white" 
                               style="font-size: 10px; padding: 4px 8px;"
                               title="Ver no Goodreads">
                                <i class="fas fa-star me-1"></i>Goodreads
                            </a>
                            @endif

                            @if($book->hasPdfLink())
                            <a href="{{ $book->pdf_link }}" 
                               target="_blank"
                               class="btn btn-danger btn-sm" 
                               style="font-size: 10px; padding: 4px 8px;"
                               title="Download PDF">
                                <i class="fas fa-file-pdf me-1"></i>PDF
                            </a>
                            @endif
                        </div>
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

<style>
.books-widget .book-item {
    transition: all 0.2s ease;
}

.books-widget .book-item:hover {
    transform: translateX(2px);
}

.books-widget .book-cover {
    transition: transform 0.2s ease;
}

.books-widget .book-cover:hover {
    transform: scale(1.05);
}

.books-widget .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.books-widget .book-title {
    font-weight: 600;
}

.books-widget .stars {
    font-size: 12px;
    line-height: 1;
}
</style>

@else
<div class="sidebar-widget books-widget mb-4" 
     style="background-color: {{ $widgetConfig?->background_color ?? '#ffffff' }};">
    
    <div class="widget-header" 
         style="background-color: {{ $widgetConfig?->title_color ?? '#1e40af' }};">
        <h5 class="widget-title">
            <i class="fas fa-book me-2"></i>{{ $widgetConfig?->display_name ?? 'LIVROS RECOMENDADOS' }}
        </h5>
    </div>
    
    <div class="widget-content text-center">
        <div class="no-books-message">
            <div class="no-books-icon text-muted mb-2">
                <i class="fas fa-book" style="font-size: 2.5rem; opacity: 0.3;"></i>
            </div>
            <p class="text-muted small mb-0">
                Nenhum livro recomendado no momento
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