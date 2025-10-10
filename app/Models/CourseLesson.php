<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_module_id',
        'title',
        'slug',
        'description',
        'type',
        'video_url',
        'video_platform',
        'video_id',
        'video_duration',
        'content',
        'quiz_data',
        'is_free',
        'is_published',
        'requires_previous',
        'min_watch_percentage',
        'sort_order',
        'live_at',
    ];

    protected $casts = [
        'video_duration' => 'integer',
        'quiz_data' => 'array',
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'requires_previous' => 'boolean',
        'min_watch_percentage' => 'integer',
        'sort_order' => 'integer',
        'live_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title);
            }
        });
    }

    // Relacionamentos
    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function course()
    {
        return $this->hasOneThrough(Course::class, CourseModule::class, 'id', 'id', 'course_module_id', 'course_id');
    }

    public function materials()
    {
        return $this->hasMany(LessonMaterial::class)->orderBy('sort_order');
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
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

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    // Métodos
    public function getFormattedDuration()
    {
        if (!$this->video_duration) {
            return '0m';
        }

        $hours = floor($this->video_duration / 3600);
        $minutes = floor(($this->video_duration % 3600) / 60);
        $seconds = $this->video_duration % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }
        return "{$seconds}s";
    }

    public function getEmbedUrl()
    {
        if (!$this->video_url || !$this->video_platform) {
            return null;
        }

        switch ($this->video_platform) {
            case 'youtube':
                return "https://www.youtube.com/embed/{$this->video_id}";
            case 'vimeo':
                return "https://player.vimeo.com/video/{$this->video_id}";
            default:
                return $this->video_url;
        }
    }

    public function extractVideoId()
    {
        if (!$this->video_url) {
            return null;
        }

        // YouTube
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $match)) {
            $this->video_platform = 'youtube';
            $this->video_id = $match[1];
            return $match[1];
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/', $this->video_url, $match)) {
            $this->video_platform = 'vimeo';
            $this->video_id = $match[1];
            return $match[1];
        }

        return null;
    }

    public function getProgressFor($enrollmentId)
    {
        $progress = $this->progress()->where('course_enrollment_id', $enrollmentId)->first();
        return $progress ? $progress : new LessonProgress([
            'course_enrollment_id' => $enrollmentId,
            'course_lesson_id' => $this->id,
            'is_completed' => false,
            'watch_percentage' => 0,
        ]);
    }

    public function isCompletedBy($enrollmentId)
    {
        return $this->progress()
            ->where('course_enrollment_id', $enrollmentId)
            ->where('is_completed', true)
            ->exists();
    }

    public function isAccessibleFor($enrollmentId)
    {
        // Se é gratuita, sempre acessível
        if ($this->is_free) {
            return true;
        }

        // Verificar se requer aula anterior
        if ($this->requires_previous) {
            $previousLesson = $this->module->lessons()
                ->where('sort_order', '<', $this->sort_order)
                ->orderBy('sort_order', 'desc')
                ->first();

            if ($previousLesson && !$previousLesson->isCompletedBy($enrollmentId)) {
                return false;
            }
        }

        return true;
    }

    public function canBeMarkedComplete($watchPercentage)
    {
        return $watchPercentage >= $this->min_watch_percentage;
    }
}