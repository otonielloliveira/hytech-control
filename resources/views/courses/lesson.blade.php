@extends('layouts.blog')

@section('title', $lesson->title . ' - ' . $course->title)

@section('content')
<div class="lesson-container">
    <!-- Lesson Header -->
    <div class="lesson-header bg-dark text-white py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="lesson-breadcrumb mb-2">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb bg-transparent p-0 m-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('courses.learning', $course->slug) }}" class="text-light">
                                        {{ $course->title }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item text-light">{{ $module->title }}</li>
                                <li class="breadcrumb-item active text-white" aria-current="page">
                                    {{ $lesson->title }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <h4 class="mb-0">{{ $lesson->title }}</h4>
                </div>
                <div class="col-md-4 text-right">
                    <div class="lesson-actions">
                        @if($previousLesson)
                        <a href="{{ route('courses.lesson', [$course->slug, $previousLesson->module->slug ?? $module->slug, $previousLesson->slug]) }}" 
                           class="btn btn-outline-light btn-sm mr-2">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                        @endif
                        
                        @if($nextLesson)
                        <a href="{{ route('courses.lesson', [$course->slug, $nextLesson->module->slug ?? $module->slug, $nextLesson->slug]) }}" 
                           class="btn btn-outline-light btn-sm">
                            Próxima <i class="fas fa-chevron-right"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="lesson-content">
        <div class="row no-gutters">
            <!-- Video Player Area -->
            <div class="col-lg-9">
                <div class="video-container">
                    @if($lesson->video_url)
                        @if(str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
                            @php
                                // Extract YouTube video ID
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $lesson->video_url, $matches);
                                $youtube_id = $matches[1] ?? null;
                            @endphp
                            
                            @if($youtube_id)
                            <div class="youtube-player">
                                <iframe id="lesson-video" 
                                        src="https://www.youtube.com/embed/{{ $youtube_id }}?enablejsapi=1&rel=0&modestbranding=1" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>
                            </div>
                            @endif
                        @elseif(str_contains($lesson->video_url, 'vimeo.com'))
                            @php
                                preg_match('/vimeo\.com\/(\d+)/', $lesson->video_url, $matches);
                                $vimeo_id = $matches[1] ?? null;
                            @endphp
                            
                            @if($vimeo_id)
                            <div class="vimeo-player">
                                <iframe id="lesson-video" 
                                        src="https://player.vimeo.com/video/{{ $vimeo_id }}" 
                                        frameborder="0" 
                                        allow="autoplay; fullscreen; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>
                            </div>
                            @endif
                        @else
                            <div class="video-player">
                                <video id="lesson-video" controls class="w-100">
                                    <source src="{{ $lesson->video_url }}" type="video/mp4">
                                    Seu navegador não suporta o elemento de vídeo.
                                </video>
                            </div>
                        @endif
                    @else
                        <div class="no-video bg-light d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="fas fa-video fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Vídeo não disponível</h5>
                                <p class="text-muted">Esta aula não possui vídeo associado.</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Lesson Content -->
                <div class="lesson-info p-4">
                    <div class="lesson-description mb-4">
                        @if($lesson->description)
                        <h5>Sobre esta aula</h5>
                        <div class="content">
                            {!! nl2br(e($lesson->description)) !!}
                        </div>
                        @endif
                    </div>
                    
                    <!-- Lesson Materials -->
                    @if($lesson->materials->count() > 0)
                    <div class="lesson-materials">
                        <h5>Materiais de Apoio</h5>
                        <div class="materials-list">
                            @foreach($lesson->materials as $material)
                            <div class="material-item d-flex align-items-center p-3 border rounded mb-2">
                                <div class="material-icon mr-3">
                                    @switch($material->type)
                                        @case('pdf')
                                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                            @break
                                        @case('document')
                                            <i class="fas fa-file-word fa-2x text-primary"></i>
                                            @break
                                        @case('presentation')
                                            <i class="fas fa-file-powerpoint fa-2x text-warning"></i>
                                            @break
                                        @case('link')
                                            <i class="fas fa-external-link-alt fa-2x text-info"></i>
                                            @break
                                        @default
                                            <i class="fas fa-file fa-2x text-secondary"></i>
                                    @endswitch
                                </div>
                                <div class="material-info flex-grow-1">
                                    <h6 class="mb-1">{{ $material->title }}</h6>
                                    @if($material->description)
                                    <p class="text-muted mb-1">{{ $material->description }}</p>
                                    @endif
                                    <small class="text-muted">{{ ucfirst($material->type) }}</small>
                                </div>
                                <div class="material-action">
                                    @if($material->type === 'link')
                                    <a href="{{ $material->file_path }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-external-link-alt"></i> Acessar
                                    </a>
                                    @else
                                    <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-3 lesson-sidebar bg-light">
                <div class="sidebar-content">
                    <!-- Progress Section -->
                    <div class="progress-section p-3 border-bottom">
                        <h6 class="mb-3">Progresso do Curso</h6>
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $enrollment->progress_percentage }}%" 
                                 aria-valuenow="{{ $enrollment->progress_percentage }}" 
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted">{{ $enrollment->progress_percentage }}% concluído</small>
                        
                        <!-- Mark as Complete Button -->
                        @if(!$progress->is_completed)
                        <button id="mark-complete-btn" class="btn btn-success btn-sm btn-block mt-3">
                            <i class="fas fa-check"></i> Marcar como Concluída
                        </button>
                        @else
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-check-circle"></i> Aula concluída!
                        </div>
                        @endif
                    </div>
                    
                    <!-- Course Modules -->
                    <div class="modules-section">
                        <h6 class="p-3 mb-0 border-bottom">Conteúdo do Curso</h6>
                        
                        @foreach($course->modules->sortBy('sort_order') as $courseModule)
                        <div class="module-section">
                            <div class="module-header p-3 {{ $courseModule->id === $module->id ? 'bg-primary text-white' : 'bg-white' }}">
                                <h6 class="mb-0">
                                    <i class="fas fa-folder-open mr-2"></i>
                                    {{ $courseModule->title }}
                                </h6>
                            </div>
                            
                            @if($courseModule->id === $module->id)
                            <div class="lessons-list">
                                @foreach($courseModule->lessons->sortBy('sort_order') as $moduleLesson)
                                @php
                                    $lessonProgress = $moduleLesson->progress->where('course_enrollment_id', $enrollment->id)->first();
                                    $isCompleted = $lessonProgress && $lessonProgress->is_completed;
                                    $isAccessible = $moduleLesson->isAccessibleFor($enrollment->id);
                                    $isCurrent = $moduleLesson->id === $lesson->id;
                                @endphp
                                <div class="lesson-item {{ $isCurrent ? 'current' : '' }}">
                                    @if($isAccessible)
                                    <a href="{{ route('courses.lesson', [$course->slug, $courseModule->slug, $moduleLesson->slug]) }}" 
                                       class="lesson-link d-flex align-items-center p-2 {{ $isCompleted ? 'completed' : '' }}">
                                        <div class="lesson-status mr-2">
                                            @if($isCompleted)
                                            <i class="fas fa-check-circle text-success"></i>
                                            @elseif($isCurrent)
                                            <i class="fas fa-play-circle text-primary"></i>
                                            @else
                                            <i class="far fa-play-circle text-muted"></i>
                                            @endif
                                        </div>
                                        <div class="lesson-info flex-grow-1">
                                            <div class="lesson-title">{{ $moduleLesson->title }}</div>
                                            @if($lesson->video_duration)
                                            <small class="lesson-duration text-muted">{{ round($lesson->video_duration / 60) }} min</small>
                                            @endif
                                        </div>
                                    </a>
                                    @else
                                    <div class="lesson-link d-flex align-items-center p-2 disabled">
                                        <div class="lesson-status mr-2">
                                            <i class="fas fa-lock text-muted"></i>
                                        </div>
                                        <div class="lesson-info flex-grow-1">
                                            <div class="lesson-title text-muted">{{ $moduleLesson->title }}</div>
                                            @if($moduleLesson->video_duration)
                                            <small class="lesson-duration text-muted">{{ round($moduleLesson->video_duration / 60) }} min</small>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.lesson-container {
    height: 100vh;
    display: flex;
    flex-direction: column;
}

.lesson-header {
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.lesson-content {
    flex: 1;
    overflow: hidden;
}

.video-container {
    position: relative;
    height: 60vh;
    background: #000;
}

.youtube-player iframe,
.vimeo-player iframe,
.video-player video {
    width: 100%;
    height: 100%;
}

.no-video {
    height: 100%;
}

.lesson-sidebar {
    height: 100vh;
    overflow-y: auto;
    border-left: 1px solid #dee2e6;
}

.lesson-link {
    color: #333;
    text-decoration: none;
    transition: all 0.2s ease;
}

.lesson-link:hover {
    background-color: #f8f9fa !important;
    color: #007bff;
    text-decoration: none;
}

.lesson-link.completed {
    background-color: #e8f5e8;
}

.lesson-item.current .lesson-link {
    background-color: #e3f2fd;
    border-left: 3px solid #007bff;
}

.lesson-title {
    font-size: 0.85rem;
    line-height: 1.3;
}

.lesson-duration {
    font-size: 0.75rem;
}

.material-item {
    transition: all 0.2s ease;
}

.material-item:hover {
    background-color: #f8f9fa;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: #ccc;
}

.breadcrumb-item a {
    color: #ccc;
}

.breadcrumb-item a:hover {
    color: #fff;
}

@media (max-width: 992px) {
    .lesson-sidebar {
        height: auto;
        border-left: none;
        border-top: 1px solid #dee2e6;
    }
    
    .video-container {
        height: 40vh;
    }
    
    .lesson-actions {
        margin-top: 1rem;
    }
}
</style>

<!-- Progress Tracking Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const markCompleteBtn = document.getElementById('mark-complete-btn');
    
    if (markCompleteBtn) {
        markCompleteBtn.addEventListener('click', function() {
            fetch('{{ route('courses.lesson.complete', [$course->slug, $module->slug, $lesson->slug]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    watch_percentage: 100,
                    watch_duration: {{ $lesson->video_duration ?? 0 }}
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI
                    markCompleteBtn.outerHTML = '<div class="alert alert-success mt-3 mb-0"><i class="fas fa-check-circle"></i> Aula concluída!</div>';
                    
                    // Update progress bar
                    const progressBar = document.querySelector('.progress-bar');
                    if (progressBar) {
                        progressBar.style.width = data.course_progress + '%';
                        progressBar.setAttribute('aria-valuenow', data.course_progress);
                        progressBar.parentElement.nextElementSibling.textContent = data.course_progress + '% concluído';
                    }
                    
                    // Mark lesson as completed in sidebar
                    const currentLessonIcon = document.querySelector('.lesson-item.current .lesson-status i');
                    if (currentLessonIcon) {
                        currentLessonIcon.className = 'fas fa-check-circle text-success';
                    }
                    
                    // Show success message
                    if (data.lesson_completed) {
                        // Could show a toast notification here
                        console.log('Lesson completed successfully!');
                    }
                }
            })
            .catch(error => {
                console.error('Error marking lesson as complete:', error);
                alert('Erro ao marcar aula como concluída. Tente novamente.');
            });
        });
    }
});
</script>
@endsection