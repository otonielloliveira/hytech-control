@props(['section', 'posts'])

@if($posts->count() > 0)
<section class="blog-section {{ $section['key'] }}-section">
    <div class="container-fluid">
        <div class="section-header">
            <div class="section-badge" style="background: linear-gradient(135deg, {{ $section['config']->section_icon ? '#667eea' : '#764ba2' }} 0%, #764ba2 100%);">
                <i class="{{ $section['config']->section_icon ?? 'fas fa-newspaper' }}"></i>
                {{ $section['config']->section_name }}
            </div>
            
            @if($section['config']->section_description)
                <p class="section-subtitle">{{ $section['config']->section_description }}</p>
            @endif
        </div>
        
        <div class="posts-grid">
            @foreach($posts as $post)
                <article class="post-card">
                    <div class="post-image-wrapper">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                 alt="{{ $post->title }}" 
                                 loading="lazy" 
                                 class="post-image">
                        @else
                            <img src="{{ asset('images/default-no-image.png') }}" 
                                 alt="{{ $post->title }}" 
                                 loading="lazy" 
                                 class="post-image">
                        @endif
                        
                        @if($post->category)
                            <span class="post-category" style="background-color: {{ $post->category->color }}">
                                {{ $post->category->name }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="post-content">
                        <h3 class="post-title">
                            <a href="{{ route('blog.post.show', $post->slug) }}">
                                {{ $post->title }}
                            </a>
                        </h3>
                        
                        @if($post->excerpt)
                            <p class="post-excerpt">{{ Str::limit($post->excerpt, 150) }}</p>
                        @endif
                        
                        <div class="post-footer">
                            <div class="post-meta">
                                <div class="meta-item">
                                    <i class="fas fa-user"></i>
                                    <span>{{ $post->user->name }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ $post->published_at->format('d/m/Y') }}</span>
                                </div>
                                @if($post->reading_time)
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $post->reading_time }} min</span>
                                    </div>
                                @endif
                            </div>
                            
                            <a href="{{ route('blog.post.show', $post->slug) }}" class="post-link">
                                Ler mais <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        
        @if($posts->count() >= 4)
            <div class="text-center mt-4">
                <a href="{{ route('posts.list', ['destination' => $section['key']]) }}" class="btn btn-outline-primary">
                    Ver todos em {{ $section['config']->section_name }}
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        @endif
    </div>
</section>

<style>
.blog-section {
    padding: 3rem 0;
    margin-bottom: 2rem;
}

.section-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.section-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    color: white;
    font-weight: 600;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    margin-bottom: 1rem;
}

.section-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
    margin: 0;
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.post-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.post-image-wrapper {
    position: relative;
    overflow: hidden;
    height: 200px;
}

.post-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.post-card:hover .post-image {
    transform: scale(1.05);
}

.post-category {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.post-content {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.post-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.4;
}

.post-title a {
    color: #212529;
    text-decoration: none;
    transition: color 0.3s ease;
}

.post-title a:hover {
    color: #667eea;
}

.post-excerpt {
    color: #6c757d;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    flex-grow: 1;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

.post-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.meta-item i {
    font-size: 0.8rem;
}

.post-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.post-link:hover {
    color: #764ba2;
    transform: translateX(5px);
}

@media (max-width: 768px) {
    .posts-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .post-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>
@endif
