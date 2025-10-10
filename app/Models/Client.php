<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'birth_date',
        'gender',
        'document_number',
        'avatar',
        'bio',
        'preferences',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'preferences' => 'array',
        'last_login_at' => 'datetime',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(ClientAddress::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(ClientAddress::class)->where('is_default', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function activeCourseEnrollments(): HasMany
    {
        return $this->courseEnrollments()->where('status', 'active');
    }

    public function completedCourseEnrollments(): HasMany
    {
        return $this->courseEnrollments()->where('status', 'completed');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
                    ->withPivot(['status', 'progress_percentage', 'started_at', 'completed_at', 'certificate_number'])
                    ->withTimestamps();
    }

    public function isEnrolledIn($courseId): bool
    {
        return $this->courseEnrollments()->where('course_id', $courseId)->exists();
    }

    public function getEnrollmentFor($courseId)
    {
        return $this->courseEnrollments()->where('course_id', $courseId)->first();
    }

    public function getCourseProgress($courseId): int
    {
        $enrollment = $this->getEnrollmentFor($courseId);
        return $enrollment ? $enrollment->progress_percentage : 0;
    }

    public function hasCertificateFor($courseId): bool
    {
        $enrollment = $this->getEnrollmentFor($courseId);
        return $enrollment && $enrollment->certificate_issued_at;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}
