@extends('layouts.blog')

@section('title', $post->meta_title ?? $post->title)
@section('description', $post->meta_description ?? $post->excerpt)

@section('content')
    <!-- Banner Carousel - Fixo em todas as telas -->
    @php
        $banners = App\Models\Banner::where('is_active', true)->orderBy('sort_order')->get();
    @endphp
    @if($banners->count() > 0)
        <div class="blog-banner">
            <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach($banners as $index => $banner)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}"></button>
                    @endforeach
                </div>
                
                <div class="carousel-inner">
                    @foreach($banners as $index => $banner)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" 
                             style="background-image: url('{{ $banner->image_url }}');">
                            <div class="carousel-overlay">
                                <div class="container">
                                    <div class="carousel-content">
                                        <h1>{{ $banner->title }}</h1>
                                        @if($banner->subtitle)
                                            <h2>{{ $banner->subtitle }}</h2>
                                        @endif
                                        @if($banner->description)
                                            <p>{{ $banner->description }}</p>
                                        @endif
                                        @if($banner->link_url)
                                            <a href="{{ $banner->link_url }}" class="btn-hero" 
                                               target="{{ $banner->target }}">
                                                {{ $banner->button_text }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($banners->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                @endif
            </div>
        </div>
    @endif

    <!-- Post Header -->
    <article class="post-detail">
        @if($post->featured_image)
            <div class="post-hero" style="background-image: url('{{ asset('storage/' . $post->featured_image) }}');">
                <div class="post-hero-overlay">
                    <div class="container">
                        <div class="post-hero-content">
                            <h1 class="post-title">{{ $post->title }}</h1>
                            
                            <div class="post-meta">
                                @if($post->category)
                                    <span class="category-badge" style="background-color: {{ $post->category->color }};">
                                        {{ $post->category->name }}
                                    </span>
                                @endif
                                
                                <span class="meta-item">
                                    <i class="fas fa-user"></i>
                                    {{ $post->user->name }}
                                </span>
                                
                                <span class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    {{ $post->published_at->format('d/m/Y') }}
                                </span>
                                
                                <span class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    {{ $post->reading_time }} min de leitura
                                </span>
                                
                                <span class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    {{ $post->views_count }} visualizações
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="container">
                <div class="post-header py-5">
                    <h1 class="post-title">{{ $post->title }}</h1>
                    
                    <div class="post-meta">
                        @if($post->category)
                            <span class="category-badge" style="background-color: {{ $post->category->color }};">
                                {{ $post->category->name }}
                            </span>
                        @endif
                        
                        <span class="meta-item">
                            <i class="fas fa-user"></i>
                            {{ $post->user->name }}
                        </span>
                        
                        <span class="meta-item">
                            <i class="fas fa-calendar"></i>
                            {{ $post->published_at->format('d/m/Y') }}
                        </span>
                        
                        <span class="meta-item">
                            <i class="fas fa-clock"></i>
                            {{ $post->reading_time }} min de leitura
                        </span>
                        
                        <span class="meta-item">
                            <i class="fas fa-eye"></i>
                            {{ $post->views_count }} visualizações
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Post Content -->
        <div class="container-fluid">
            @php
                $sidebarConfig = App\Services\SidebarService::getSidebarConfig();
                $showSidebar = $sidebarConfig['show_sidebar'];
                $sidebarPosition = $sidebarConfig['position'] ?? 'right';
            @endphp
            
            <div class="row">
                @if($showSidebar && $sidebarPosition === 'left')
                    <div class="col-lg-3">
                        @include('layouts.sidebar')
                    </div>
                @endif
                
                <div class="@if($showSidebar) col-lg-9 @else col-lg-12 @endif">
                    <div class="container">
                        <div class="post-content">
                        @if($post->excerpt)
                            <div class="post-excerpt">
                                <p class="lead">{{ $post->excerpt }}</p>
                            </div>
                        @endif

                        <!-- Vídeo (se configurado para aparecer no conteúdo) -->
                        @if($post->show_video_in_content && $post->video_embed)
                            <div class="post-video mb-4">
                                {!! $post->video_embed !!}
                            </div>
                        @endif

                        <div class="post-body">
                            {!! $post->processed_content !!}
                        </div>

                        <!-- Tags -->
                        @if($post->tags && $post->tags->count() > 0)
                            <div class="post-tags mt-4">
                                <h6>Tags:</h6>
                                @foreach($post->tags as $tag)
                                    <a href="{{ route('blog.tag.show', $tag->slug) }}" 
                                       class="badge bg-secondary me-2 mb-2 text-decoration-none">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <!-- Share Buttons -->
                        <div class="share-buttons mt-4 pt-4 border-top">
                            <h6>Compartilhar:</h6>
                            <div class="share-icons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" 
                                   target="_blank" class="btn btn-facebook">
                                    <i class="fab fa-facebook-f"></i> Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" 
                                   target="_blank" class="btn btn-twitter">
                                    <i class="fab fa-twitter"></i> Twitter
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" 
                                   target="_blank" class="btn btn-linkedin">
                                    <i class="fab fa-linkedin-in"></i> LinkedIn
                                </a>
                                <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . request()->fullUrl()) }}" 
                                   target="_blank" class="btn btn-whatsapp">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="comments-section mt-5">
                        <div class="section-title">
                            <h3>💬 Comentários ({{ $post->approved_comments_count }})</h3>
                        </div>

                        <!-- Comment Form -->
                        @if($config->allow_comments)
                            <div class="comment-form-wrapper">
                                <form action="{{ route('blog.comment.store', $post) }}" method="POST" class="comment-form">
                                    @csrf
                                    
                                    @guest
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="author_name" class="form-label">Nome *</label>
                                                <input type="text" name="author_name" id="author_name" 
                                                       class="form-control @error('author_name') is-invalid @enderror" 
                                                       value="{{ old('author_name') }}" required>
                                                @error('author_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="author_email" class="form-label">E-mail *</label>
                                                <input type="email" name="author_email" id="author_email" 
                                                       class="form-control @error('author_email') is-invalid @enderror" 
                                                       value="{{ old('author_email') }}" required>
                                                @error('author_email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-user me-2"></i>Comentando como: <strong>{{ auth()->user()->name }}</strong>
                                    </div>
                                    @endguest
                                    
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Comentário *</label>
                                        <textarea name="content" id="content" rows="4" 
                                                  class="form-control @error('content') is-invalid @enderror" 
                                                  required>{{ old('content') }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Enviar Comentário
                                    </button>
                                </form>
                            </div>
                        @endif

                        <!-- Comments List -->
                        @if($post->approvedComments->count() > 0)
                            <div class="comments-list mt-4">
                                @foreach($post->approvedComments as $comment)
                                    <div class="comment-item">
                                        <div class="comment-header">
                                            <h6 class="comment-author">{{ $comment->name }}</h6>
                                            <small class="comment-date text-muted">
                                                {{ $comment->created_at->format('d/m/Y \à\s H:i') }}
                                            </small>
                                        </div>
                                        <div class="comment-body">
                                            <p>{{ $comment->comment }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">Seja o primeiro a comentar!</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar">
                        <!-- Posts Relacionados -->
                        @if($relatedPosts->count() > 0)
                            <div class="sidebar-widget">
                                <h5 class="widget-title">📰 Posts Relacionados</h5>
                                <div class="related-posts">
                                    @foreach($relatedPosts as $relatedPost)
                                        <article class="related-post">
                                            @if($relatedPost->featured_image)
                                                <img src="{{ asset('storage/' . $relatedPost->featured_image) }}" 
                                                     alt="{{ $relatedPost->title }}">
                                            @else
                                                <img src="{{ asset('images/default-no-image.png') }}" 
                                                     alt="{{ $relatedPost->title }}">
                                            @endif
                                            <div class="related-post-content">
                                                <h6>
                                                    <a href="{{ route('blog.post.show', $relatedPost->slug) }}">
                                                        {{ $relatedPost->title }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $relatedPost->published_at->format('d/m/Y') }}
                                                </small>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Social Links -->
                        @if($config->facebook_url || $config->instagram_url || $config->twitter_url || $config->youtube_url)
                            <div class="sidebar-widget">
                                <h5 class="widget-title">🌐 Siga-nos</h5>
                                <div class="social-links">
                                    @if($config->facebook_url)
                                        <a href="{{ $config->facebook_url }}" target="_blank" class="btn btn-facebook">
                                            <i class="fab fa-facebook-f"></i> Facebook
                                        </a>
                                    @endif
                                    @if($config->instagram_url)
                                        <a href="{{ $config->instagram_url }}" target="_blank" class="btn btn-instagram">
                                            <i class="fab fa-instagram"></i> Instagram
                                        </a>
                                    @endif
                                    @if($config->twitter_url)
                                        <a href="{{ $config->twitter_url }}" target="_blank" class="btn btn-twitter">
                                            <i class="fab fa-twitter"></i> Twitter
                                        </a>
                                    @endif
                                    @if($config->youtube_url)
                                        <a href="{{ $config->youtube_url }}" target="_blank" class="btn btn-youtube">
                                            <i class="fab fa-youtube"></i> YouTube
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                        </div>
                    </div>
                    
                    @if($showSidebar && $sidebarPosition === 'right')
                        <div class="col-lg-3">
                            @include('layouts.sidebar')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </article>
@endsection

@section('styles')
<style>
    /* Video Container - Responsivo */
    .video-container {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        margin: 1rem 0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }
    
    .post-video {
        margin: 2rem 0;
    }

    .post-hero {
        height: 400px;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .post-hero-overlay {
        background: rgba(0,0,0,0.6);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
    }
    
    .post-hero-content {
        position: relative;
        z-index: 2;
        color: white;
    }
    
    .post-hero-content .post-title {
        color: white;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .post-hero-content .post-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .post-hero-content .meta-item {
        color: rgba(255,255,255,0.9);
        font-size: 0.9rem;
    }
    
    .post-hero-content .category-badge {
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .post-content {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .post-excerpt {
        border-left: 4px solid var(--primary-color);
        padding-left: 1rem;
        margin-bottom: 2rem;
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 5px;
    }
    
    .post-body img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        margin: 1rem 0;
    }
    
    .share-buttons .share-icons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .share-buttons .btn {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        text-decoration: none;
        font-size: 0.9rem;
        border: none;
    }
    
    .btn-facebook { background: #1877f2; color: white; }
    .btn-twitter { background: #1da1f2; color: white; }
    .btn-linkedin { background: #0077b5; color: white; }
    .btn-whatsapp { background: #25d366; color: white; }
    .btn-instagram { background: #e4405f; color: white; }
    .btn-youtube { background: #ff0000; color: white; }
    
    .comments-section {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .comment-item {
        border-bottom: 1px solid #eee;
        padding: 1rem 0;
    }
    
    .comment-item:last-child {
        border-bottom: none;
    }
    
    .comment-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .sidebar-widget {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .widget-title {
        color: var(--primary-color);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color);
    }
    
    .related-post {
        display: flex;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
    }
    
    .related-post:last-child {
        border-bottom: none;
    }
    
    .related-post img {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .related-post-content h6 {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    
    .related-post-content a {
        color: var(--text-color);
        text-decoration: none;
    }
    
    .related-post-content a:hover {
        color: var(--primary-color);
    }
    
    .social-links {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .social-links .btn {
        border: none;
        text-align: left;
        border-radius: 5px;
    }
    
    @media (max-width: 768px) {
        .post-hero {
            height: 250px;
        }
        
        .post-hero-content .post-title {
            font-size: 1.8rem;
        }
        
        .post-content {
            padding: 1rem;
        }
        
        .comments-section {
            padding: 1rem;
        }
    }
</style>
@endsection