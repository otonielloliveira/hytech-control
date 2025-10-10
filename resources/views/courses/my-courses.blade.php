@extends('layouts.app')

@section('title', 'Meus Cursos')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Meus Cursos</h1>
            
            @if($enrollments->count() > 0)
            <div class="row">
                @foreach($enrollments as $enrollment)
                @php $course = $enrollment->course; @endphp
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card course-enrollment-card h-100">
                        @if($course->image)
                        <img src="{{ asset('storage/' . $course->image) }}" 
                             class="card-img-top" alt="{{ $course->title }}" 
                             style="height: 200px; object-fit: cover;">
                        @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="fas fa-graduation-cap fa-3x text-muted"></i>
                        </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <!-- Status Badge -->
                            <div class="mb-2">
                                @switch($enrollment->status)
                                    @case('active')
                                        <span class="badge badge-success">Ativo</span>
                                        @break
                                    @case('completed')
                                        <span class="badge badge-primary">Concluído</span>
                                        @break
                                    @case('pending')
                                        <span class="badge badge-warning">Pendente</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge badge-danger">Cancelado</span>
                                        @break
                                @endswitch
                                
                                <span class="badge badge-secondary ml-1">{{ ucfirst($course->level) }}</span>
                            </div>
                            
                            <h5 class="card-title">{{ $course->title }}</h5>
                            <p class="card-text flex-grow-1">{{ Str::limit($course->description, 100) }}</p>
                            
                            <!-- Progress Bar -->
                            @if($enrollment->status === 'active' || $enrollment->status === 'completed')
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Progresso</small>
                                    <small class="text-muted">{{ $enrollment->progress_percentage }}%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $enrollment->progress_percentage }}%" 
                                         aria-valuenow="{{ $enrollment->progress_percentage }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Course Info -->
                            <div class="course-meta mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> Matriculado em {{ $enrollment->created_at->format('d/m/Y') }}
                                </small>
                                @if($enrollment->started_at)
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-play"></i> Iniciado em {{ $enrollment->started_at->format('d/m/Y') }}
                                </small>
                                @endif
                                @if($enrollment->completed_at)
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-check"></i> Concluído em {{ $enrollment->completed_at->format('d/m/Y') }}
                                </small>
                                @endif
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="mt-auto">
                                @if($enrollment->status === 'active')
                                    <a href="{{ route('courses.learning', $course->slug) }}" 
                                       class="btn btn-primary btn-block">
                                        <i class="fas fa-play"></i> 
                                        {{ $enrollment->progress_percentage > 0 ? 'Continuar' : 'Iniciar' }} Curso
                                    </a>
                                @elseif($enrollment->status === 'completed')
                                    <div class="btn-group btn-block" role="group">
                                        <a href="{{ route('courses.learning', $course->slug) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i> Revisar
                                        </a>
                                        @if($enrollment->certificate_issued_at)
                                        <a href="{{ route('courses.certificate', $course->slug) }}" 
                                           class="btn btn-success">
                                            <i class="fas fa-certificate"></i> Certificado
                                        </a>
                                        @endif
                                    </div>
                                @elseif($enrollment->status === 'pending')
                                    <div class="alert alert-warning mb-2">
                                        <small>
                                            <i class="fas fa-clock"></i> 
                                            Aguardando confirmação do pagamento
                                        </small>
                                    </div>
                                    <a href="{{ route('courses.show', $course->slug) }}" 
                                       class="btn btn-outline-primary btn-block">
                                        Ver Curso
                                    </a>
                                @else
                                    <a href="{{ route('courses.show', $course->slug) }}" 
                                       class="btn btn-outline-secondary btn-block">
                                        Ver Curso
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($enrollments->hasPages())
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $enrollments->links() }}
                    </div>
                </div>
            </div>
            @endif
            
            @else
            <!-- Empty State -->
            <div class="row">
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-graduation-cap fa-5x text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">Você ainda não possui cursos</h3>
                        <p class="text-muted mb-4">Explore nosso catálogo e encontre o curso perfeito para você!</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-search"></i> Explorar Cursos
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.course-enrollment-card {
    transition: transform 0.2s ease-in-out;
    border: 1px solid #dee2e6;
}

.course-enrollment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.progress {
    background-color: #e9ecef;
}

.progress-bar {
    background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
}

.course-meta {
    font-size: 0.875rem;
    line-height: 1.4;
}

.course-meta i {
    width: 14px;
    text-align: center;
    margin-right: 4px;
}

.btn-group.btn-block {
    display: flex;
    width: 100%;
}

.btn-group.btn-block .btn {
    flex: 1;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.3;
}

.badge {
    font-size: 0.75em;
}

@media (max-width: 768px) {
    .btn-group.btn-block {
        flex-direction: column;
    }
    
    .btn-group.btn-block .btn {
        margin-bottom: 0.25rem;
    }
    
    .btn-group.btn-block .btn:last-child {
        margin-bottom: 0;
    }
}
</style>
@endsection