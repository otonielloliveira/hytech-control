<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'image',
        'trailer_video',
        'certificate_type_id',
        'level',
        'status',
        'price',
        'promotional_price',
        'promotion_ends_at',
        'estimated_hours',
        'max_enrollments',
        'requirements',
        'what_you_will_learn',
        'target_audience',
        'instructor_notes',
        'is_featured',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'promotional_price' => 'decimal:2',
        'promotion_ends_at' => 'datetime',
        'estimated_hours' => 'integer',
        'max_enrollments' => 'integer',
        'requirements' => 'array',
        'what_you_will_learn' => 'array',
        'target_audience' => 'array',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
            if (empty($course->published_at) && $course->status === 'published') {
                $course->published_at = now();
            }
        });

        static::updating(function ($course) {
            if ($course->isDirty('status') && $course->status === 'published' && !$course->published_at) {
                $course->published_at = now();
            }
        });
    }

    // Relacionamentos
    public function certificateType()
    {
        return $this->belongsTo(CertificateType::class);
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order');
    }

    public function lessons()
    {
        return $this->hasManyThrough(CourseLesson::class, CourseModule::class);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function activeEnrollments()
    {
        return $this->enrollments()->where('status', 'active');
    }

    public function completedEnrollments()
    {
        return $this->enrollments()->where('status', 'completed');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    // Métodos
    public function getCurrentPrice()
    {
        if ($this->promotional_price && $this->promotion_ends_at && $this->promotion_ends_at > now()) {
            return $this->promotional_price;
        }
        return $this->price;
    }

    public function hasActivePromotion()
    {
        return $this->promotional_price && $this->promotion_ends_at && $this->promotion_ends_at > now();
    }

    public function getDiscountPercentage()
    {
        if (!$this->hasActivePromotion()) {
            return 0;
        }
        return round((($this->price - $this->promotional_price) / $this->price) * 100);
    }

    public function getTotalLessons()
    {
        return $this->lessons()->where('is_published', true)->count();
    }

    public function getTotalDuration()
    {
        return $this->lessons()->where('is_published', true)->sum('video_duration');
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

    public function getEnrollmentCount()
    {
        return $this->enrollments()->count();
    }

    public function hasAvailableSlots()
    {
        if (!$this->max_enrollments) {
            return true;
        }
        return $this->getEnrollmentCount() < $this->max_enrollments;
    }

    public function isEnrolledBy($clientId)
    {
        return $this->enrollments()->where('client_id', $clientId)->exists();
    }

    public function getImageUrl()
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return asset('images/course-placeholder.jpg');
    }

    public function getProgressFor($clientId)
    {
        $enrollment = $this->enrollments()->where('client_id', $clientId)->first();
        return $enrollment ? $enrollment->progress_percentage : 0;
    }
}