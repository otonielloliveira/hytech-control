<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'course_id',
        'status',
        'paid_amount',
        'payment_method',
        'payment_transaction_id',
        'progress_percentage',
        'started_at',
        'completed_at',
        'expires_at',
        'certificate_issued_at',
        'certificate_number',
        'quiz_scores',
        'final_score',
        'notes',
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'progress_percentage' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'certificate_issued_at' => 'datetime',
        'quiz_scores' => 'array',
        'final_score' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($enrollment) {
            if (empty($enrollment->started_at)) {
                $enrollment->started_at = now();
            }
        });
    }

    // Relacionamentos
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function completedLessons()
    {
        return $this->lessonProgress()->where('is_completed', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    // Métodos
    public function calculateProgress()
    {
        $totalLessons = $this->course->getTotalLessons();
        if ($totalLessons === 0) {
            return 100;
        }

        $completedLessons = $this->completedLessons()->count();
        $percentage = round(($completedLessons / $totalLessons) * 100);
        
        $this->update(['progress_percentage' => $percentage]);
        
        // Verificar se completou o curso
        if ($percentage >= 100 && $this->status === 'active') {
            $this->markAsCompleted();
        }
        
        return $percentage;
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress_percentage' => 100,
        ]);

        // Gerar certificado se aplicável
        if ($this->canIssueCertificate()) {
            $this->issueCertificate();
        }
    }

    public function canIssueCertificate()
    {
        if (!$this->course->certificateType) {
            return false;
        }

        $certificateType = $this->course->certificateType;
        return $certificateType->canIssueCertificate($this->progress_percentage, $this->final_score);
    }

    public function issueCertificate()
    {
        if ($this->certificate_issued_at) {
            return; // Já foi emitido
        }

        $certificateNumber = $this->generateCertificateNumber();
        
        $this->update([
            'certificate_issued_at' => now(),
            'certificate_number' => $certificateNumber,
        ]);
    }

    protected function generateCertificateNumber()
    {
        $prefix = 'CERT';
        $courseCode = strtoupper(substr($this->course->slug, 0, 4));
        $year = now()->year;
        $sequential = str_pad($this->id, 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$courseCode}-{$year}-{$sequential}";
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function getRemainingDays()
    {
        if (!$this->expires_at) {
            return null;
        }
        
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function getTimeSpent()
    {
        return $this->lessonProgress()->sum('watch_duration');
    }

    public function getFormattedTimeSpent()
    {
        $totalSeconds = $this->getTimeSpent();
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }

    public function getAverageQuizScore()
    {
        if (!$this->quiz_scores) {
            return null;
        }
        
        $scores = array_values($this->quiz_scores);
        return count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : null;
    }


}