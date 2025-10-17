@extends('layouts.blog')

@section('title', 'Todas as Postagens - ' . ($config->site_name ?? 'Blog'))
@section('description', 'Explore todas as postagens do nosso blog com filtros avançados por data, categoria e tipo de conteúdo.')

@push('styles')
<style>
    .filters-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .filter-card {
        background: rgba(255,255,255,0.95);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .post-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    
    .post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    
    .post-image {
        height: 200px;
        overflow: hidden;
        position: relative;
    }
    
    .post-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .post-card:hover .post-image img {
        transform: scale(1.05);
    }
    
    .category-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 15px;
        font-weight: 600;
    }
    
    .post-meta {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .post-title {
        font-size: 1.1rem;
        font-weight: 600;
        line-height: 1.4;
        margin-bottom: 0.5rem;
    }
    
    .post-excerpt {
        font-size: 0.9rem;
        color: #6c757d;
        line-height: 1.5;
    }
    
    .results-info {
        background: rgba(255,255,255,0.9);
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .btn-filter {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
        color: white;
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e0e6ed;
        padding: 8px 12px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header da Página -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="text-center">
                <h1 class="display-5 fw-bold text-primary mb-2">
                    <i class="fas fa-newspaper me-3"></i>Todas as Postagens
                </h1>
                <p class="lead text-muted">Explore nosso conteúdo completo com filtros avançados</p>
            </div>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="filters-container p-4 mb-4">
        <form method="GET" action="{{ route('posts.list') }}" id="filtersForm">
            <div class="filter-card p-4">
                <div class="row g-3">
                    <!-- Busca -->
                    <div class="col-lg-3 col-md-6">
                        <label for="search" class="form-label fw-semibold">
                            <i class="fas fa-search me-1"></i>Buscar
                        </label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Título, conteúdo..." value="{{ request('search') }}">
                    </div>
                    
                    <!-- Categoria -->
                    <div class="col-lg-2 col-md-6">
                        <label for="category" class="form-label fw-semibold">
                            <i class="fas fa-folder me-1"></i>Categoria
                        </label>
                        <select class="form-select" id="category" name="category">
                            <option value="all">Todas</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->posts_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Seção/Destino -->
                    <div class="col-lg-2 col-md-6">
                        <label for="destination" class="form-label fw-semibold">
                            <i class="fas fa-tags me-1"></i>Seção
                        </label>
                        <select class="form-select" id="destination" name="destination">
                            <option value="all">Todas</option>
                            @foreach($destinations as $key => $label)
                                <option value="{{ $key }}" 
                                        {{ request('destination') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filtro de Data -->
                    <div class="col-lg-2 col-md-6">
                        <label for="date_filter" class="form-label fw-semibold">
                            <i class="fas fa-calendar me-1"></i>Período
                        </label>
                        <select class="form-select" id="date_filter" name="date_filter">
                            <option value="">Qualquer data</option>
                            <option value="last_week" {{ request('date_filter') == 'last_week' ? 'selected' : '' }}>
                                Última semana
                            </option>
                            <option value="last_month" {{ request('date_filter') == 'last_month' ? 'selected' : '' }}>
                                Último mês
                            </option>
                            <option value="last_3_months" {{ request('date_filter') == 'last_3_months' ? 'selected' : '' }}>
                                Últimos 3 meses
                            </option>
                            <option value="last_year" {{ request('date_filter') == 'last_year' ? 'selected' : '' }}>
                                Último ano
                            </option>
                        </select>
                    </div>
                    
                    <!-- Ordenação -->
                    <div class="col-lg-2 col-md-6">
                        <label for="order_by" class="form-label fw-semibold">
                            <i class="fas fa-sort me-1"></i>Ordenar por
                        </label>
                        <select class="form-select" id="order_by" name="order_by">
                            <option value="published_at" {{ request('order_by', 'published_at') == 'published_at' ? 'selected' : '' }}>
                                Data de publicação
                            </option>
                            <option value="title" {{ request('order_by') == 'title' ? 'selected' : '' }}>
                                Título (A-Z)
                            </option>
                            <option value="views" {{ request('order_by') == 'views' ? 'selected' : '' }}>
                                Mais visualizados
                            </option>
                        </select>
                    </div>
                    
                    <!-- Direção da Ordenação -->
                    <div class="col-lg-1 col-md-6">
                        <label for="order_direction" class="form-label fw-semibold">Ordem</label>
                        <select class="form-select" id="order_direction" name="order_direction">
                            <option value="desc" {{ request('order_direction', 'desc') == 'desc' ? 'selected' : '' }}>
                                ↓ Desc
                            </option>
                            <option value="asc" {{ request('order_direction') == 'asc' ? 'selected' : '' }}>
                                ↑ Asc
                            </option>
                        </select>
                    </div>
                </div>
                
                <!-- Data Customizada -->
                <div class="row g-3 mt-2" id="customDateFilters" style="display: none;">
                    <div class="col-lg-3">
                        <label for="date_from" class="form-label fw-semibold">Data Inicial</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-lg-3">
                        <label for="date_to" class="form-label fw-semibold">Data Final</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                </div>
                
                <!-- Botões -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-filter">
                                <i class="fas fa-filter me-1"></i>Aplicar Filtros
                            </button>
                            <a href="{{ route('posts.list') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpar
                            </a>
                            <button type="button" class="btn btn-outline-primary" id="toggleCustomDate">
                                <i class="fas fa-calendar-alt me-1"></i>Data Customizada
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Informações dos Resultados -->
    @if(request()->anyFilled(['search', 'category', 'destination', 'date_filter', 'date_from', 'date_to']))
        <div class="results-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">
                        <i class="fas fa-search-plus text-primary me-2"></i>
                        Resultados da busca: <strong>{{ $posts->total() }}</strong> post(s) encontrado(s)
                    </h6>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @if(request('search'))
                            <span class="badge bg-primary">Busca: "{{ request('search') }}"</span>
                        @endif
                        @if(request('category') && request('category') !== 'all')
                            @php $selectedCategory = $categories->find(request('category')) @endphp
                            <span class="badge bg-success">Categoria: {{ $selectedCategory->name ?? 'N/A' }}</span>
                        @endif
                        @if(request('destination') && request('destination') !== 'all')
                            <span class="badge bg-info">Seção: {{ $destinations[request('destination')] ?? 'N/A' }}</span>
                        @endif
                        @if(request('date_filter'))
                            <span class="badge bg-warning text-dark">Período: {{ ucfirst(str_replace('_', ' ', request('date_filter'))) }}</span>
                        @endif
                    </div>
                </div>
                <small class="text-muted">
                    Página {{ $posts->currentPage() }} de {{ $posts->lastPage() }}
                </small>
            </div>
        </div>
    @else
        <div class="results-info">
            <h6 class="mb-0">
                <i class="fas fa-list text-primary me-2"></i>
                Exibindo <strong>{{ $posts->total() }}</strong> postagens
                <small class="text-muted ms-2">• Página {{ $posts->currentPage() }} de {{ $posts->lastPage() }}</small>
            </h6>
        </div>
    @endif
    
    <!-- Grid de Posts -->
    @if($posts->count() > 0)
        <div class="row g-4">
            @foreach($posts as $post)
                <div class="col-lg-4 col-md-6">
                    <article class="card post-card h-100">
                        <!-- Imagem do Post -->
                        <div class="post-image">
                            @if($post->featured_image)
                                <img src="{{ Storage::url($post->featured_image) }}" 
                                     alt="{{ $post->title }}" 
                                     loading="lazy">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-newspaper text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            
                            <!-- Badge da Categoria -->
                            @if($post->category)
                                <span class="badge category-badge" 
                                      style="background-color: {{ $post->category->color ?? '#6c757d' }};">
                                    {{ $post->category->name }}
                                </span>
                            @endif
                        </div>
                        
                        <!-- Conteúdo do Card -->
                        <div class="card-body p-3 d-flex flex-column">
                            <!-- Meta informações -->
                            <div class="post-meta mb-2 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user me-1"></i>
                                    <span>{{ $post->user->name ?? 'Autor' }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar me-1"></i>
                                    <span>{{ $post->published_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            
                            <!-- Título -->
                            <h5 class="post-title">
                                <a href="{{ route('blog.post.show', $post->slug) }}" 
                                   class="text-decoration-none text-dark">
                                    {{ $post->title }}
                                </a>
                            </h5>
                            
                            <!-- Excerpt -->
                            <p class="post-excerpt flex-grow-1">
                                {{ Str::limit($post->excerpt, 120) }}
                            </p>
                            
                            <!-- Footer do Card -->
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
                                <div class="d-flex gap-3">
                                    @if($post->views_count > 0)
                                        <small class="text-muted">
                                            <i class="fas fa-eye me-1"></i>{{ number_format($post->views_count) }}
                                        </small>
                                    @endif
                                    
                                    @if($post->reading_time > 0)
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $post->reading_time }} min
                                        </small>
                                    @endif
                                </div>
                                
                                <!-- Seção/Destino -->
                                @if($post->destination)
                                    <span class="badge bg-light text-dark" style="font-size: 0.7rem;">
                                        {{ $destinations[$post->destination] ?? $post->destination }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        
        <!-- Paginação -->
        @if($posts->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $posts->links() }}
            </div>
        @endif
    @else
        <!-- Nenhum resultado -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-search text-muted" style="font-size: 4rem;"></i>
            </div>
            <h4 class="text-muted mb-3">Nenhum post encontrado</h4>
            <p class="text-muted mb-4">
                Tente ajustar os filtros ou 
                <a href="{{ route('posts.list') }}" class="text-primary text-decoration-none">
                    limpar a busca
                </a>
            </p>
            <a href="{{ route('blog.index') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Voltar ao Início
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle de data customizada
    const toggleCustomDate = document.getElementById('toggleCustomDate');
    const customDateFilters = document.getElementById('customDateFilters');
    
    toggleCustomDate.addEventListener('click', function() {
        if (customDateFilters.style.display === 'none') {
            customDateFilters.style.display = 'flex';
            toggleCustomDate.innerHTML = '<i class="fas fa-times me-1"></i>Ocultar Data';
        } else {
            customDateFilters.style.display = 'none';
            toggleCustomDate.innerHTML = '<i class="fas fa-calendar-alt me-1"></i>Data Customizada';
            // Limpar campos de data
            document.getElementById('date_from').value = '';
            document.getElementById('date_to').value = '';
        }
    });
    
    // Auto-submit do formulário ao mudar filtros
    const filterSelects = document.querySelectorAll('#category, #destination, #date_filter, #order_by, #order_direction');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Se mudou para data customizada, não submeter ainda
            if (this.id === 'date_filter' && this.value === 'custom') {
                customDateFilters.style.display = 'flex';
                toggleCustomDate.innerHTML = '<i class="fas fa-times me-1"></i>Ocultar Data';
                return;
            }
            
            // Se não é data customizada, limpar campos de data e submeter
            if (this.id === 'date_filter' && this.value !== 'custom') {
                document.getElementById('date_from').value = '';
                document.getElementById('date_to').value = '';
                customDateFilters.style.display = 'none';
                toggleCustomDate.innerHTML = '<i class="fas fa-calendar-alt me-1"></i>Data Customizada';
            }
            
            document.getElementById('filtersForm').submit();
        });
    });
    
    // Submit do formulário ao pressionar Enter no campo de busca
    document.getElementById('search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('filtersForm').submit();
        }
    });
    
    // Mostrar campos de data customizada se já estão preenchidos
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    if (dateFrom || dateTo) {
        customDateFilters.style.display = 'flex';
        toggleCustomDate.innerHTML = '<i class="fas fa-times me-1"></i>Ocultar Data';
    }
});
</script>
@endpush