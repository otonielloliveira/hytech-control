@extends('layouts.blog')

@section('title', 'Tag: ' . $tag->name)
@section('description', 'Posts relacionados à tag: ' . $tag->name)

@section('content')
    
    <!-- Tag Header -->
    <div class="tag-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="tag-info">
                        <div class="tag-icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        
                        <div class="tag-details">
                            <h1 class="tag-title">
                                #{{ $tag->name }}
                            </h1>
                            
                            <p class="tag-description">
                                Posts relacionados à tag "{{ $tag->name }}"
                            </p>
                            
                            <div class="tag-stats">
                                <span class="badge bg-primary">
                                    {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 text-lg-end">
                    <div class="tag-actions">
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
                                    @if($post->category)
                                        <span class="post-category" style="background-color: {{ $post->category->color }};">
                                            {{ $post->category->name }}
                                        </span>
                                    @endif
                                    
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
                                            @foreach($post->tags->take(3) as $postTag)
                                                <a href="{{ route('blog.tag.show', $postTag->slug) }}" 
                                                   class="badge {{ $postTag->slug === $tag->slug ? 'bg-primary' : 'bg-secondary' }} text-decoration-none me-1">
                                                    #{{ $postTag->name }}
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
                        <nav aria-label="Navegação da tag">
                            {{ $posts->links('pagination::bootstrap-4') }}
                        </nav>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-hashtag fa-3x text-muted mb-3"></i>
                        <h3>Nenhum post encontrado</h3>
                        <p class="text-muted">
                            Ainda não há posts publicados com esta tag.
                        </p>
                        <a href="{{ route('blog.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar ao Blog
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Popular Tags -->
    @if($popularTags->count() > 0)
        <section class="section" style="background: var(--light-bg);">
            <div class="container">
                <div class="section-title text-center">
                    <h2>🏷️ Tags Populares</h2>
                    <p>Explore outros temas populares do nosso blog</p>
                </div>
                
                <div class="tags-cloud text-center">
                    @foreach($popularTags as $popularTag)
                        <a href="{{ route('blog.tag.show', $popularTag->slug) }}" 
                           class="tag-cloud-item {{ $popularTag->slug === $tag->slug ? 'active' : '' }}"
                           style="font-size: {{ 0.8 + ($popularTag->posts_count * 0.1) }}rem;">
                            #{{ $popularTag->name }}
                            <span class="tag-count">({{ $popularTag->posts_count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@section('styles')
<style>
    .tag-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 0 2rem;
        margin-bottom: 2rem;
    }
    
    .tag-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .tag-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        border: 4px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(10px);
    }
    
    .tag-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: white;
    }
    
    .tag-description {
        font-size: 1.1rem;
        color: rgba(255,255,255,0.9);
        margin-bottom: 1rem;
    }
    
    .tag-stats .badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        background: rgba(255,255,255,0.2) !important;
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
    }
    
    .tag-actions .btn-outline-primary {
        border-color: white;
        color: white;
    }
    
    .tag-actions .btn-outline-primary:hover {
        background: white;
        color: var(--primary-color);
    }
    
    .tags-cloud {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .tag-cloud-item {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: white;
        color: var(--text-color);
        text-decoration: none;
        border-radius: 25px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .tag-cloud-item:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .tag-cloud-item.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .tag-count {
        font-size: 0.8em;
        opacity: 0.7;
        font-weight: normal;
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
        .tag-header {
            padding: 2rem 0 1rem;
        }
        
        .tag-info {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .tag-icon {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }
        
        .tag-title {
            font-size: 2rem;
        }
        
        .tag-actions {
            text-align: center;
            margin-top: 1rem;
        }
        
        .tags-cloud {
            gap: 0.5rem;
        }
        
        .tag-cloud-item {
            font-size: 0.9rem !important;
            padding: 0.4rem 0.8rem;
        }
    }
</style>
@endsection