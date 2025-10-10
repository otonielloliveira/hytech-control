@extends('layouts.blog')

@section('title', $course->title . ' - Área de Aprendizado')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar with Course Navigation -->
        <div class="col-lg-3 course-sidebar bg-light">
            <div class="sidebar-content p-3">
                <!-- Course Header -->
                <div class="course-header mb-4">
                    <h5 class="course-title">{{ $course->title }}</h5>
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $enrollment->progress_percentage }}%" 
                             aria-valuenow="{{ $enrollment->progress_percentage }}" 
                             aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted">{{ $enrollment->progress_percentage }}% concluído</small>
                </div>
                
                <!-- Course Modules -->
                <div class="course-modules">
                    @foreach($course->modules->sortBy('sort_order') as $module)
                    <div class="module-item mb-3">
                        <div class="module-header">
                            <h6 class="module-title mb-2">
                                <i class="fas fa-folder-open text-primary mr-2"></i>
                                {{ $module->title }}
                            </h6>
                        </div>
                        
                        <div class="lessons-list">
                            @foreach($module->lessons->sortBy('sort_order') as $lesson)
                            @php
                                $lessonProgress = $lesson->progress->where('course_enrollment_id', $enrollment->id)->first();
                                $isCompleted = $lessonProgress && $lessonProgress->is_completed;
                                $isAccessible = $lesson->isAccessibleFor($enrollment->id);
                            @endphp
                            <div class="lesson-item">
                                @if($isAccessible)
                                <a href="{{ route('courses.lesson', [$course->slug, $module->slug, $lesson->slug]) }}" 
                                   class="lesson-link d-flex align-items-center py-2 px-3 {{ $isCompleted ? 'completed' : '' }}">
                                    <div class="lesson-status mr-2">
                                        @if($isCompleted)
                                        <i class="fas fa-check-circle text-success"></i>
                                        @else
                                        <i class="fas fa-play-circle text-primary"></i>
                                        @endif
                                    </div>
                                    <div class="lesson-info flex-grow-1">
                                        <div class="lesson-title">{{ $lesson->title }}</div>
                                        @if($lesson->video_duration)
                                        <small class="lesson-duration text-muted">{{ round($lesson->video_duration / 60) }} min</small>
                                        @endif
                                    </div>
                                </a>
                                @else
                                <div class="lesson-link d-flex align-items-center py-2 px-3 disabled">
                                    <div class="lesson-status mr-2">
                                        <i class="fas fa-lock text-muted"></i>
                                    </div>
                                    <div class="lesson-info flex-grow-1">
                                        <div class="lesson-title text-muted">{{ $lesson->title }}</div>
                                        @if($lesson->video_duration)
                                        <small class="lesson-duration text-muted">{{ round($lesson->video_duration / 60) }} min</small>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Main Content Area -->
        <div class="col-lg-9 course-content">
            <div class="content-header p-3 bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Área de Aprendizado</h4>
                        <small class="text-muted">{{ $course->title }}</small>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-outline-secondary mr-2">
                            <i class="fas fa-info-circle"></i> Sobre o Curso
                        </a>
                        <a href="{{ route('courses.my-courses') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Meus Cursos
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="content-body p-4">
                @if($nextLesson)
                <!-- Welcome Message with Next Lesson -->
                <div class="welcome-section mb-5">
                    <div class="row">
                        <div class="col-lg-8">
                            <h2 class="mb-3">
                                @if($enrollment->progress_percentage == 0)
                                    Bem-vindo ao curso!
                                @else
                                    Continue de onde parou
                                @endif
                            </h2>
                            <p class="lead text-muted mb-4">
                                @if($enrollment->progress_percentage == 0)
                                    Você está prestes a iniciar uma jornada de aprendizado incrível. Clique no botão abaixo para começar sua primeira aula.
                                @else
                                    Você já completou {{ $enrollment->progress_percentage }}% do curso. Continue sua jornada de aprendizado!
                                @endif
                            </p>
                            
                            <a href="{{ route('courses.lesson', [$course->slug, $nextLesson->module->slug, $nextLesson->slug]) }}" 
                               class="btn btn-primary btn-lg">
                                <i class="fas fa-play"></i> 
                                {{ $enrollment->progress_percentage == 0 ? 'Iniciar' : 'Continuar' }} Curso
                            </a>
                        </div>
                        <div class="col-lg-4">
                            <div class="next-lesson-card card">
                                <div class="card-body">
                                    <h6 class="card-title">Próxima Aula:</h6>
                                    <h5 class="text-primary">{{ $nextLesson->title }}</h5>
                                    <p class="card-text">{{ $nextLesson->module->title }}</p>
                                    @if($nextLesson->video_duration)
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> {{ round($nextLesson->video_duration / 60) }} minutos
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <!-- Course Completed -->
                <div class="completion-section text-center mb-5">
                    <div class="completion-icon mb-4">
                        <i class="fas fa-trophy fa-5x text-warning"></i>
                    </div>
                    <h2 class="mb-3">Parabéns! Você concluiu o curso!</h2>
                    <p class="lead text-muted mb-4">
                        Você completou todas as aulas de "{{ $course->title }}". 
                        Agora você pode baixar seu certificado de conclusão.
                    </p>
                    
                    @if($enrollment->certificate_issued_at)
                    <a href="{{ route('courses.certificate', $course->slug) }}" 
                       class="btn btn-success btn-lg mr-3">
                        <i class="fas fa-certificate"></i> Baixar Certificado
                    </a>
                    @endif
                    
                    <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-search"></i> Explorar Mais Cursos
                    </a>
                </div>
                @endif
                
                <!-- Course Statistics -->
                <div class="course-stats">
                    <h4 class="mb-4">Estatísticas do Curso</h4>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card card text-center">
                                <div class="card-body">
                                    <i class="fas fa-play-circle fa-2x text-primary mb-2"></i>
                                    <h5 class="card-title">{{ $course->modules->sum(fn($m) => $m->lessons->count()) }}</h5>
                                    <p class="card-text">Total de Aulas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card card text-center">
                                <div class="card-body">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h5 class="card-title">{{ $enrollment->completedLessons()->count() }}</h5>
                                    <p class="card-text">Aulas Concluídas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card card text-center">
                                <div class="card-body">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <h5 class="card-title">{{ $course->estimated_hours }}h</h5>
                                    <p class="card-text">Duração Total</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card card text-center">
                                <div class="card-body">
                                    <i class="fas fa-percentage fa-2x text-info mb-2"></i>
                                    <h5 class="card-title">{{ $enrollment->progress_percentage }}%</h5>
                                    <p class="card-text">Progresso</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.course-sidebar {
    height: 100vh;
    overflow-y: auto;
    position: sticky;
    top: 0;
}

.course-title {
    font-size: 1rem;
    line-height: 1.3;
    margin-bottom: 0.5rem;
}

.module-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #333;
}

.lesson-link {
    display: block;
    color: #333;
    text-decoration: none;
    border-radius: 4px;
    margin-bottom: 1px;
    transition: all 0.2s ease;
}

.lesson-link:hover {
    background-color: #f8f9fa;
    color: #007bff;
    text-decoration: none;
}

.lesson-link.completed {
    background-color: #e8f5e8;
}

.lesson-link.disabled {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

.lesson-title {
    font-size: 0.85rem;
    line-height: 1.3;
}

.lesson-duration {
    font-size: 0.75rem;
}

.content-header {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.next-lesson-card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.stat-card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.completion-icon {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

@media (max-width: 992px) {
    .course-sidebar {
        height: auto;
        position: static;
    }
    
    .header-actions {
        display: flex;
        flex-direction: column;
    }
    
    .header-actions .btn {
        margin-bottom: 0.5rem;
        margin-right: 0 !important;
    }
}
</style>
@endsection