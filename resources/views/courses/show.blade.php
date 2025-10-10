@extends('layouts.blog')

@section('title', $course->title . ' - Curso')

@section('content')
<div class="container">
    <!-- Course Header -->
    <div class="row mb-5">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Cursos</a></li>
                    <li class="breadcrumb-item active">{{ $course->title }}</li>
                </ol>
            </nav>
            
            <div class="mb-3">
                <span class="badge badge-info mr-2">{{ $course->certificateType->name ?? 'Curso' }}</span>
                <span class="badge badge-secondary mr-2">{{ ucfirst($course->level) }}</span>
                @if($course->is_featured)
                <span class="badge badge-warning">
                    <i class="fas fa-star"></i> Destaque
                </span>
                @endif
            </div>
            
            <h1 class="display-4 mb-3">{{ $course->title }}</h1>
            <p class="lead text-muted">{{ $course->description }}</p>
            
            <div class="course-stats d-flex flex-wrap mb-4">
                <div class="stat-item mr-4 mb-2">
                    <i class="fas fa-clock text-primary"></i>
                    <span class="ml-1">{{ $course->estimated_hours }} horas</span>
                </div>
                <div class="stat-item mr-4 mb-2">
                    <i class="fas fa-play-circle text-primary"></i>
                    <span class="ml-1">{{ $course->modules->sum(fn($m) => $m->lessons->count()) }} aulas</span>
                </div>
                <div class="stat-item mr-4 mb-2">
                    <i class="fas fa-users text-primary"></i>
                    <span class="ml-1">{{ $course->enrollments->count() }} alunos</span>
                </div>
                <div class="stat-item mr-4 mb-2">
                    <i class="fas fa-calendar text-primary"></i>
                    <span class="ml-1">Atualizado em {{ $course->updated_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Course Sidebar -->
        <div class="col-lg-4">
            <div class="card course-sidebar shadow">
                @if($course->image)
                <img src="{{ asset('storage/' . $course->image) }}" 
                     class="card-img-top" alt="{{ $course->title }}" style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                     style="height: 200px;">
                    <i class="fas fa-graduation-cap fa-3x text-muted"></i>
                </div>
                @endif
                
                <div class="card-body">
                    <!-- Pricing -->
                    <div class="pricing mb-4 text-center">
                        @if($course->getCurrentPrice() > 0)
                            @if($course->promotional_price && $course->promotional_price < $course->price)
                                <div class="original-price">
                                    <span class="text-muted"><s>R$ {{ number_format($course->price, 2, ',', '.') }}</s></span>
                                </div>
                                <div class="current-price">
                                    <h3 class="text-primary mb-0">R$ {{ number_format($course->promotional_price, 2, ',', '.') }}</h3>
                                </div>
                                @php
                                    $discount = round((($course->price - $course->promotional_price) / $course->price) * 100);
                                @endphp
                                <small class="text-success">{{ $discount }}% de desconto</small>
                            @else
                                <h3 class="text-primary mb-0">R$ {{ number_format($course->price, 2, ',', '.') }}</h3>
                            @endif
                        @else
                            <h3 class="text-success mb-0">Gratuito</h3>
                        @endif
                    </div>
                    
                    <!-- Enrollment Button -->
                    @auth('client')
                        @if($enrollment)
                            @if($enrollment->status === 'active')
                                <a href="{{ route('courses.learning', $course->slug) }}" 
                                   class="btn btn-success btn-lg btn-block mb-3">
                                    <i class="fas fa-play"></i> Continuar Curso
                                </a>
                                <div class="progress mb-3">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $progress }}%" 
                                         aria-valuenow="{{ $progress }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ $progress }}%
                                    </div>
                                </div>
                            @elseif($enrollment->status === 'pending')
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock"></i> Aguardando pagamento
                                </div>
                            @elseif($enrollment->status === 'completed')
                                <a href="{{ route('courses.certificate', $course->slug) }}" 
                                   class="btn btn-primary btn-lg btn-block mb-3">
                                    <i class="fas fa-certificate"></i> Baixar Certificado
                                </a>
                            @endif
                        @else
                            <form action="{{ route('courses.enroll', $course->slug) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg btn-block mb-3">
                                    <i class="fas fa-plus"></i> 
                                    {{ $course->getCurrentPrice() > 0 ? 'Comprar Curso' : 'Matricular-se Grátis' }}
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('client.login') }}" class="btn btn-primary btn-lg btn-block mb-3">
                            <i class="fas fa-user"></i> Entrar para se Matricular
                        </a>
                    @endauth
                    
                    <!-- Course Info -->
                    <div class="course-info">
                        <h6 class="font-weight-bold mb-3">Este curso inclui:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-play-circle text-primary mr-2"></i>
                                {{ $course->modules->sum(fn($m) => $m->lessons->count()) }} aulas em vídeo
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-download text-primary mr-2"></i>
                                Materiais para download
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-mobile-alt text-primary mr-2"></i>
                                Acesso pelo celular e TV
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-infinity text-primary mr-2"></i>
                                Acesso vitalício
                            </li>
                            @if($course->certificateType)
                            <li class="mb-2">
                                <i class="fas fa-certificate text-primary mr-2"></i>
                                Certificado {{ $course->certificateType->name }}
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Course Content -->
    <div class="row">
        <div class="col-lg-8">
            <!-- About Course -->
            <div class="course-section mb-5">
                <h3 class="mb-4">Sobre o Curso</h3>
                <div class="content">
                    {!! nl2br(e($course->description)) !!}
                </div>
            </div>
            
            <!-- Course Curriculum -->
            <div class="course-section mb-5">
                <h3 class="mb-4">Conteúdo do Curso</h3>
                <div class="curriculum">
                    @foreach($course->modules->sortBy('sort_order') as $module)
                    <div class="module-item mb-3">
                        <div class="card">
                            <div class="card-header" id="module-{{ $module->id }}">
                                <h5 class="mb-0">
                                    <button class="btn btn-link w-100 text-left" type="button" 
                                            data-toggle="collapse" data-target="#collapse-{{ $module->id }}" 
                                            aria-expanded="true" aria-controls="collapse-{{ $module->id }}">
                                        <i class="fas fa-play-circle mr-2"></i>
                                        {{ $module->title }}
                                        <span class="badge badge-secondary float-right">
                                            {{ $module->lessons->count() }} aulas
                                        </span>
                                    </button>
                                </h5>
                            </div>
                            
                            <div id="collapse-{{ $module->id }}" class="collapse {{ $loop->first ? 'show' : '' }}" 
                                 aria-labelledby="module-{{ $module->id }}">
                                <div class="card-body">
                                    @if($module->description)
                                    <p class="text-muted mb-3">{{ $module->description }}</p>
                                    @endif
                                    
                                    <div class="lessons-list">
                                        @foreach($module->lessons->sortBy('sort_order') as $lesson)
                                        <div class="lesson-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <div class="lesson-info">
                                                <i class="fas fa-play-circle text-primary mr-2"></i>
                                                <span>{{ $lesson->title }}</span>
                                                @if($lesson->estimated_duration)
                                                <small class="text-muted ml-2">({{ $lesson->estimated_duration }} min)</small>
                                                @endif
                                            </div>
                                            @if($lesson->is_preview)
                                            <span class="badge badge-info">Preview</span>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Learning Objectives -->
            @if($course->what_you_will_learn)
            <div class="course-section mb-5">
                <h3 class="mb-4">O que você vai aprender</h3>
                <div class="learning-objectives">
                    <ul>
                        @foreach($course->what_you_will_learn as $objective)
                        <li>{{ $objective }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            
            <!-- Requirements -->
            @if($course->requirements)
            <div class="course-section mb-5">
                <h3 class="mb-4">Requisitos</h3>
                <div class="requirements">
                    <ul>
                        @foreach($course->requirements as $requirement)
                        <li>{{ $requirement }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Related Courses -->
        <div class="col-lg-4">
            @if($relatedCourses->count() > 0)
            <div class="related-courses">
                <h4 class="mb-4">Cursos Relacionados</h4>
                @foreach($relatedCourses as $relatedCourse)
                <div class="card mb-3">
                    <div class="row no-gutters">
                        <div class="col-4">
                            @if($relatedCourse->image)
                            <img src="{{ asset('storage/' . $relatedCourse->image) }}" 
                                 class="card-img h-100" alt="{{ $relatedCourse->title }}" 
                                 style="object-fit: cover;">
                            @else
                            <div class="card-img h-100 bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-graduation-cap text-muted"></i>
                            </div>
                            @endif
                        </div>
                        <div class="col-8">
                            <div class="card-body p-3">
                                <h6 class="card-title mb-1">
                                    <a href="{{ route('courses.show', $relatedCourse->slug) }}" 
                                       class="text-decoration-none">
                                        {{ Str::limit($relatedCourse->title, 40) }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    {{ ucfirst($relatedCourse->level) }}
                                </small>
                                <div class="mt-2">
                                    @if($relatedCourse->getCurrentPrice() > 0)
                                    <strong class="text-primary small">
                                        R$ {{ number_format($relatedCourse->getCurrentPrice(), 2, ',', '.') }}
                                    </strong>
                                    @else
                                    <strong class="text-success small">Gratuito</strong>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.course-sidebar {
    position: sticky;
    top: 20px;
}

.stat-item {
    font-size: 14px;
}

.course-section h3 {
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

.module-item .btn-link {
    color: #333;
    text-decoration: none;
}

.module-item .btn-link:hover {
    color: #007bff;
    text-decoration: none;
}

.lesson-item:last-child {
    border-bottom: none !important;
}

.pricing h3 {
    font-size: 2rem;
    font-weight: bold;
}

.original-price {
    font-size: 1.1rem;
}

.course-info ul li {
    font-size: 14px;
}

.content, .learning-objectives, .requirements {
    line-height: 1.6;
}

@media (max-width: 768px) {
    .course-sidebar {
        position: static;
        margin-top: 2rem;
    }
    
    .display-4 {
        font-size: 2rem;
    }
}
</style>
@endsection