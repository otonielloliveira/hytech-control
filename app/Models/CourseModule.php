<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'description',
        'icon',
        'estimated_hours',
        'is_free',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'estimated_hours' => 'integer',
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($module) {
            if (empty($module->slug)) {
                $module->slug = Str::slug($module->title);
            }
        });
    }

    // Relacionamentos
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class)->orderBy('sort_order');
    }

    public function publishedLessons()
    {
        return $this->lessons()->where('is_published', true);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    // Métodos
    public function getTotalLessons()
    {
        return $this->publishedLessons()->count();
    }

    public function getTotalDuration()
    {
        return $this->publishedLessons()->sum('video_duration');
    }

    public function getFormattedDuration()
    {
        $totalSeconds = $this->getTotalDuration();
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }

    public function getProgressFor($enrollmentId)
    {
        $totalLessons = $this->getTotalLessons();
        if ($totalLessons === 0) {
            return 100;
        }

        $completedLessons = LessonProgress::where('course_enrollment_id', $enrollmentId)
            ->whereIn('course_lesson_id', $this->lessons()->pluck('id'))
            ->where('is_completed', true)
            ->count();

        return round(($completedLessons / $totalLessons) * 100);
    }

    public function isAccessibleFor($enrollmentId)
    {
        // Se é gratuito, sempre acessível
        if ($this->is_free) {
            return true;
        }

        // Verificar se o aluno tem matrícula ativa
        $enrollment = CourseEnrollment::find($enrollmentId);
        return $enrollment && $enrollment->status === 'active';
    }
}