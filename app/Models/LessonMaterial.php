<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LessonMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_lesson_id',
        'title',
        'description',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'external_url',
        'is_downloadable',
        'requires_completion',
        'sort_order',
    ];

    protected $casts = [
        'is_downloadable' => 'boolean',
        'requires_completion' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relacionamentos
    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    // Scopes
    public function scopeDownloadable($query)
    {
        return $query->where('is_downloadable', true);
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
    public function getDownloadUrl()
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }
        return $this->external_url;
    }

    public function getFormattedSize()
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIconClass()
    {
        return match($this->type) {
            'pdf' => 'fas fa-file-pdf text-danger',
            'document' => 'fas fa-file-word text-primary',
            'presentation' => 'fas fa-file-powerpoint text-warning',
            'worksheet' => 'fas fa-file-excel text-success',
            'link' => 'fas fa-external-link-alt text-info',
            'video' => 'fas fa-video text-danger',
            'audio' => 'fas fa-volume-up text-purple',
            'image' => 'fas fa-image text-pink',
            default => 'fas fa-file text-secondary',
        };
    }

    public function isAccessibleFor($enrollmentId)
    {
        if (!$this->requires_completion) {
            return true;
        }

        return $this->lesson->isCompletedBy($enrollmentId);
    }
}