<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lecture extends Model
{
    use HasFactory;

    protected $table = 'blog_lectures';

    protected $fillable = [
        'title',
        'description',
        'speaker',
        'image',
        'date_time',
        'location',
        'link_url',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'date_time' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('date_time', 'asc');
    }

    // Methods
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public static function getActiveLectures(int $limit = 3)
    {
        return static::active()
            ->ordered()
            ->take($limit)
            ->get();
    }
}
