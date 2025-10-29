<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Helpers\HtmlHelper;

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

    public function scopeUpcoming($query)
    {
        return $query->where('date_time', '>=', now())
                    ->orderBy('date_time', 'asc');
    }

    // Methods
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getFormattedStartDateAttribute(): ?string
    {
        return $this->date_time ? $this->date_time->format('d/m/Y H:i') : null;
    }

    public function getFormattedDescriptionAttribute(): ?string
    {
        return HtmlHelper::processContent($this->description);
    }

    public static function getActiveLectures(int $limit = 3)
    {
        return static::active()
            ->ordered()
            ->take($limit)
            ->get();
    }

    public static function upcoming(int $limit = 3)
    {
        return static::active()
            ->upcoming()
            ->take($limit)
            ->get();
    }
}
