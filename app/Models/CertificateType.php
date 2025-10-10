<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CertificateType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'template_file',
        'template_config',
        'min_completion_percentage',
        'requires_exam',
        'min_exam_score',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'template_config' => 'array',
        'min_completion_percentage' => 'integer',
        'requires_exam' => 'boolean',
        'min_exam_score' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificateType) {
            if (empty($certificateType->slug)) {
                $certificateType->slug = Str::slug($certificateType->name);
            }
        });
    }

    // Relacionamentos
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Métodos
    public function canIssueCertificate($completionPercentage, $examScore = null)
    {
        if ($completionPercentage < $this->min_completion_percentage) {
            return false;
        }

        if ($this->requires_exam && $examScore < $this->min_exam_score) {
            return false;
        }

        return true;
    }
}