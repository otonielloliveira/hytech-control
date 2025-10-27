<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'video_url',
        'video_platform',
        'video_id',
        'thumbnail_url',
        'duration',
        'published_date',
        'category',
        'tags',
        'is_active',
        'priority',
        'views_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_date' => 'date',
        'priority' => 'integer',
        'views_count' => 'integer',
        'tags' => 'array',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('published_date', 'desc');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getEmbedUrlAttribute(): ?string
    {
        if (!$this->video_id) {
            return null;
        }

        return match ($this->video_platform) {
            'youtube' => "https://www.youtube.com/embed/{$this->video_id}",
            'vimeo' => "https://player.vimeo.com/video/{$this->video_id}",
            default => $this->video_url,
        };
    }

    public function getThumbnailAttribute(): string
    {
        if ($this->thumbnail_url) {
            return $this->thumbnail_url;
        }

        return match ($this->video_platform) {
            'youtube' => "https://img.youtube.com/vi/{$this->video_id}/maxresdefault.jpg",
            'vimeo' => "https://vumbnail.com/{$this->video_id}.jpg",
            default => asset('images/video-placeholder.jpg'),
        };
    }

    public function getFormattedPublishedDateAttribute(): ?string
    {
        return $this->published_date ? $this->published_date->format('d/m/Y') : null;
    }

    public function getShortDescriptionAttribute(): string
    {
        return $this->description ? Str::limit($this->description, 120) : '';
    }

    // Route binding
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Methods
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function extractVideoId(): void
    {
        if (!$this->video_url) {
            return;
        }

        // YouTube patterns
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $this->video_url, $matches)) {
            $this->video_id = $matches[1];
            $this->video_platform = 'youtube';
        } elseif (preg_match('/youtu\.be\/([^?]+)/', $this->video_url, $matches)) {
            $this->video_id = $matches[1];
            $this->video_platform = 'youtube';
        }
        // Vimeo patterns
        elseif (preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $matches)) {
            $this->video_id = $matches[1];
            $this->video_platform = 'vimeo';
        }
    }

    public static function getActiveVideos(int $limit = 12)
    {
        return static::active()
            ->ordered()
            ->take($limit)
            ->get();
    }

    public static function getCategories(): array
    {
        return static::active()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($video) {
            if (empty($video->slug)) {
                $video->slug = $video->generateUniqueSlug($video->title);
            }
        });

        static::updating(function ($video) {
            if ($video->isDirty('title') && empty($video->slug)) {
                $video->slug = $video->generateUniqueSlug($video->title);
            }
        });

        static::saving(function ($video) {
            if ($video->isDirty('video_url')) {
                $video->extractVideoId();
            }
        });
    }

    protected function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
