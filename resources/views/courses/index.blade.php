@extends('layouts.app')

@section('title', 'Cursos - Sistema de Aprendizado')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="jumbotron jumbotron-fluid bg-primary text-white py-5">
                <div class="container">
                    <h1 class="display-4 mb-3">Cursos Online</h1>
                    <p class="lead">Desenvolva suas habilidades com nossos cursos certificados</p>
                    
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('courses.index') }}" class="mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="search" class="form-control form-control-lg" 
                                       placeholder="Buscar cursos..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="level" class="form-control form-control-lg">
                                    <option value="">Todos os Níveis</option>
                                    <option value="iniciante" {{ request('level') == 'iniciante' ? 'selected' : '' }}>Iniciante</option>
                                    <option value="intermediario" {{ request('level') == 'intermediario' ? 'selected' : '' }}>Intermediário</option>
                                    <option value="avancado" {{ request('level') == 'avancado' ? 'selected' : '' }}>Avançado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-light btn-lg btn-block">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($featuredCourses->count() > 0)
    <!-- Featured Courses -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">Cursos em Destaque</h2>
            <div class="row">
                @foreach($featuredCourses as $course)
                <div class="col-md-4 mb-4">
                    <div class="card course-card h-100 shadow-sm">
                        @if($course->image)
                        <img src="{{ asset('storage/' . $course->image) }}" 
                             class="card-img-top" alt="{{ $course->title }}" style="height: 200px; object-fit: cover;">
                        @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="fas fa-graduation-cap fa-3x text-muted"></i>
                        </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <span class="badge badge-primary mb-2">{{ ucfirst($course->level) }}</span>
                            <h5 class="card-title">{{ $course->title }}</h5>
                            <p class="card-text flex-grow-1">{{ Str::limit($course->description, 100) }}</p>
                            
                            <div class="course-meta mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> {{ $course->estimated_hours }}h
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-users"></i> {{ $course->enrollments_count ?? 0 }} alunos
                                </small>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div class="price">
                                    @if($course->getCurrentPrice() > 0)
                                        @if($course->promotional_price && $course->promotional_price < $course->price)
                                            <span class="text-muted"><s>R$ {{ number_format($course->price, 2, ',', '.') }}</s></span>
                                            <strong class="text-primary">R$ {{ number_format($course->promotional_price, 2, ',', '.') }}</strong>
                                        @else
                                            <strong class="text-primary">R$ {{ number_format($course->price, 2, ',', '.') }}</strong>
                                        @endif
                                    @else
                                        <strong class="text-success">Gratuito</strong>
                                    @endif
                                </div>
                                <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-primary">
                                    Ver Curso
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Filters and Sorting -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex flex-wrap align-items-center">
                <span class="mr-3">Filtrar por:</span>
                
                <!-- Price Filter -->
                <div class="btn-group mr-3" role="group">
                    <a href="{{ route('courses.index', array_merge(request()->query(), ['price_filter' => ''])) }}" 
                       class="btn btn-sm {{ !request('price_filter') ? 'btn-primary' : 'btn-outline-primary' }}">
                        Todos
                    </a>
                    <a href="{{ route('courses.index', array_merge(request()->query(), ['price_filter' => 'free'])) }}" 
                       class="btn btn-sm {{ request('price_filter') == 'free' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Gratuitos
                    </a>
                    <a href="{{ route('courses.index', array_merge(request()->query(), ['price_filter' => 'paid'])) }}" 
                       class="btn btn-sm {{ request('price_filter') == 'paid' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Pagos
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 text-right">
            <form method="GET" action="{{ route('courses.index') }}" class="d-inline">
                @foreach(request()->query() as $key => $value)
                    @if($key != 'sort')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                
                <select name="sort" class="form-control d-inline w-auto" onchange="this.form.submit()">
                    <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Destaque</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mais Recentes</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Mais Populares</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Menor Preço</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Maior Preço</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="row">
        @forelse($courses as $course)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card course-card h-100 shadow-sm">
                @if($course->image)
                <img src="{{ asset('storage/' . $course->image) }}" 
                     class="card-img-top" alt="{{ $course->title }}" style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                     style="height: 200px;">
                    <i class="fas fa-graduation-cap fa-3x text-muted"></i>
                </div>
                @endif
                
                @if($course->is_featured)
                <div class="position-absolute" style="top: 10px; left: 10px;">
                    <span class="badge badge-warning">
                        <i class="fas fa-star"></i> Destaque
                    </span>
                </div>
                @endif
                
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge badge-info">{{ $course->certificateType->name ?? 'Curso' }}</span>
                        <span class="badge badge-secondary">{{ ucfirst($course->level) }}</span>
                    </div>
                    
                    <h5 class="card-title">{{ $course->title }}</h5>
                    <p class="card-text flex-grow-1">{{ Str::limit($course->description, 120) }}</p>
                    
                    <div class="course-meta mb-3">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> {{ $course->estimated_hours }}h
                            <span class="mx-2">|</span>
                            <i class="fas fa-play-circle"></i> {{ $course->modules->sum(fn($m) => $m->lessons->count()) }} aulas
                            <span class="mx-2">|</span>
                            <i class="fas fa-users"></i> {{ $course->enrollments_count ?? 0 }} alunos
                        </small>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <div class="price">
                            @if($course->getCurrentPrice() > 0)
                                @if($course->promotional_price && $course->promotional_price < $course->price)
                                    <div>
                                        <span class="text-muted small"><s>R$ {{ number_format($course->price, 2, ',', '.') }}</s></span>
                                    </div>
                                    <strong class="text-primary">R$ {{ number_format($course->promotional_price, 2, ',', '.') }}</strong>
                                @else
                                    <strong class="text-primary">R$ {{ number_format($course->price, 2, ',', '.') }}</strong>
                                @endif
                            @else
                                <strong class="text-success">Gratuito</strong>
                            @endif
                        </div>
                        <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-primary">
                            Ver Curso
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">Nenhum curso encontrado</h3>
                <p class="text-muted">Tente ajustar os filtros de busca</p>
                <a href="{{ route('courses.index') }}" class="btn btn-primary">Ver Todos os Cursos</a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($courses->hasPages())
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $courses->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.course-card {
    transition: transform 0.2s ease-in-out;
}

.course-card:hover {
    transform: translateY(-5px);
}

.jumbotron {
    border-radius: 0;
}

.badge {
    font-size: 0.75em;
}

.course-meta i {
    margin-right: 3px;
}

.price {
    font-size: 1.1em;
}

.price s {
    font-size: 0.9em;
}
</style>
@endsection