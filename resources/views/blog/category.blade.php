@extends('layouts.blog')

@section('title', $category->meta_title ?? 'Categoria: ' . $category->name)
@section('description', $category->meta_description ?? $category->description)

@section('content')
    

    <!-- Category Header -->
    <div class="category-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="category-info">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" 
                                 alt="{{ $category->name }}" 
                                 class="category-image">
                        @else
                            <img src="{{ asset('images/default-no-image.png') }}" 
                                 alt="{{ $category->name }}" 
                                 class="category-image">
                        @endif
                        
                        <div class="category-details">
                            <h1 class="category-title" style="color: {{ $category->color }};">
                                {{ $category->name }}
                            </h1>
                            
                            @if($category->description)
                                <p class="category-description">{{ $category->description }}</p>
                            @endif
                            
                            <div class="category-stats">
                                <span class="badge bg-primary">
                                    {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 text-lg-end">
                    <div class="category-actions">
                        <a href="{{ route('blog.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar ao Blog
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Posts Grid -->
    <section class="section">
        <div class="container">
            @if($posts->count() > 0)
                <div class="row">
                    @foreach($posts as $post)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <article class="post-card">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                         alt="{{ $post->title }}" loading="lazy">
                                @else
                                    <img src="{{ asset('images/default-no-image.png') }}" 
                                         alt="{{ $post->title }}" loading="lazy">
                                @endif
                                
                                <div class="post-card-body">
                                    <span class="post-category" style="background-color: {{ $category->color }};">
                                        {{ $category->name }}
                                    </span>
                                    
                                    <h3 class="post-title">
                                        <a href="{{ route('blog.post.show', $post->slug) }}">
                                            {{ $post->title }}
                                        </a>
                                    </h3>
                                    
                                    <p class="post-excerpt">{{ $post->excerpt }}</p>
                                    
                                    <div class="post-meta">
                                        <span>
                                            <i class="fas fa-user me-1"></i>{{ $post->user->name }}
                                        </span>
                                        <span>
                                            <i class="fas fa-calendar me-1"></i>{{ $post->published_at->format('d/m/Y') }}
                                        </span>
                                        <span>
                                            <i class="fas fa-clock me-1"></i>{{ $post->reading_time }} min
                                        </span>
                                    </div>
                                    
                                    @if($post->tags->count() > 0)
                                        <div class="post-tags mt-2">
                                            @foreach($post->tags->take(3) as $tag)
                                                <a href="{{ route('blog.tag.show', $tag->slug) }}" 
                                                   class="badge bg-secondary text-decoration-none me-1">
                                                    #{{ $tag->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                    <div class="pagination-wrapper mt-5">
                        <nav aria-label="Navegação da categoria">
                            {{ $posts->links('pagination::bootstrap-4') }}
                        </nav>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h3>Nenhum post encontrado</h3>
                        <p class="text-muted">
                            Ainda não há posts publicados nesta categoria.
                        </p>
                        <a href="{{ route('blog.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar ao Blog
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Other Categories -->
    @if($otherCategories->count() > 0)
        <section class="section" style="background: var(--light-bg);">
            <div class="container">
                <div class="section-title text-center">
                    <h2>🏷️ Outras Categorias</h2>
                    <p>Explore outros temas do nosso blog</p>
                </div>
                
                <div class="row">
                    @foreach($otherCategories as $otherCategory)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <a href="{{ route('blog.category.show', $otherCategory->slug) }}" 
                               class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm category-card">
                                    <div class="card-body text-center">
                                        @if($otherCategory->image)
                                            <img src="{{ asset('storage/' . $otherCategory->image) }}" 
                                                 alt="{{ $otherCategory->name }}" 
                                                 class="rounded-circle mb-3" 
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('images/default-no-image.png') }}" 
                                                 alt="{{ $otherCategory->name }}" 
                                                 class="rounded-circle mb-3" 
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        @endif
                                        
                                        <h6 class="card-title" style="color: {{ $otherCategory->color }};">
                                            {{ $otherCategory->name }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $otherCategory->published_posts_count }} {{ Str::plural('post', $otherCategory->published_posts_count) }}
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@section('styles')
<style>
    .category-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 3rem 0 2rem;
        margin-bottom: 2rem;
    }
    
    .category-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .category-image {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .category-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        border: 4px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .category-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .category-description {
        font-size: 1.1rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    
    .category-stats .badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    
    .category-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    .pagination-wrapper {
        display: flex;
        justify-content: center;
    }
    
    .pagination-wrapper .pagination {
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .pagination-wrapper .page-link {
        border: none;
        color: var(--primary-color);
        padding: 0.75rem 1rem;
    }
    
    .pagination-wrapper .page-link:hover {
        background-color: var(--primary-color);
        color: white;
    }
    
    .pagination-wrapper .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .empty-state {
        max-width: 400px;
        margin: 0 auto;
    }
    
    @media (max-width: 768px) {
        .category-header {
            padding: 2rem 0 1rem;
        }
        
        .category-info {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .category-image,
        .category-icon {
            width: 80px;
            height: 80px;
        }
        
        .category-title {
            font-size: 2rem;
        }
        
        .category-actions {
            text-align: center;
            margin-top: 1rem;
        }
    }
</style>
@endsection