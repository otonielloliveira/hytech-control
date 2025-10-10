<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_enrollment_id',
        'course_lesson_id',
        'is_completed',
        'watch_percentage',
        'watch_duration',
        'quiz_answers',
        'quiz_score',
        'notes',
        'started_at',
        'completed_at',
        'last_watched_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'watch_percentage' => 'integer',
        'watch_duration' => 'integer',
        'quiz_answers' => 'array',
        'quiz_score' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_watched_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($progress) {
            if (empty($progress->started_at)) {
                $progress->started_at = now();
            }
        });

        static::updating(function ($progress) {
            if ($progress->isDirty('watch_percentage') || $progress->isDirty('watch_duration')) {
                $progress->last_watched_at = now();
            }

            if ($progress->isDirty('is_completed') && $progress->is_completed && !$progress->completed_at) {
                $progress->completed_at = now();
            }
        });
    }

    // Relacionamentos
    public function enrollment()
    {
        return $this->belongsTo(CourseEnrollment::class, 'course_enrollment_id');
    }

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeInProgress($query)
    {
        return $query->where('is_completed', false)
                    ->where('watch_percentage', '>', 0);
    }

    // Métodos
    public function updateWatchProgress($percentage, $duration = null)
    {
        $this->update([
            'watch_percentage' => min(100, max(0, $percentage)),
            'watch_duration' => $duration ?: $this->watch_duration,
            'last_watched_at' => now(),
        ]);

        // Verificar se pode ser marcada como completa
        if ($this->lesson->canBeMarkedComplete($percentage) && !$this->is_completed) {
            $this->markAsCompleted();
        }
    }

    public function markAsCompleted()
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'watch_percentage' => 100,
        ]);

        // Atualizar progresso do curso
        $this->enrollment->calculateProgress();
    }

    public function submitQuiz($answers, $score = null)
    {
        $this->update([
            'quiz_answers' => $answers,
            'quiz_score' => $score,
        ]);

        // Se passou no quiz, marcar como completa
        if ($score && $score >= 70) { // Nota mínima configurável
            $this->markAsCompleted();
        }
    }

    public function getFormattedWatchTime()
    {
        if (!$this->watch_duration) {
            return '0m';
        }

        $hours = floor($this->watch_duration / 3600);
        $minutes = floor(($this->watch_duration % 3600) / 60);
        $seconds = $this->watch_duration % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }
        return "{$seconds}s";
    }

    public function getProgressStatus()
    {
        if ($this->is_completed) {
            return 'completed';
        } elseif ($this->watch_percentage > 0) {
            return 'in_progress';
        }
        return 'not_started';
    }

    public function getProgressBadgeClass()
    {
        return match($this->getProgressStatus()) {
            'completed' => 'badge bg-success',
            'in_progress' => 'badge bg-warning',
            'not_started' => 'badge bg-secondary',
        };
    }

    public function getProgressBadgeText()
    {
        return match($this->getProgressStatus()) {
            'completed' => 'Concluída',
            'in_progress' => 'Em andamento',
            'not_started' => 'Não iniciada',
        };
    }
}