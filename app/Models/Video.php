<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
            'youtube' => "https://img.youtube.com/vi/{$this->video_id}/hqdefault.jpg",
            'vimeo' => "https://vumbnail.com/{$this->video_id}.jpg",
            default => asset('images/video-placeholder.jpg'),
        };
    }

    public function getHighQualityThumbnailAttribute(): string
    {
        if ($this->thumbnail_url) {
            return $this->thumbnail_url;
        }

        return match ($this->video_platform) {
            'youtube' => "https://img.youtube.com/vi/{$this->video_id}/maxresdefault.jpg",
            'vimeo' => "https://vumbnail.com/{$this->video_id}_640x360.jpg",
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
            $this->fetchYoutubeDuration();
        } elseif (preg_match('/youtu\.be\/([^?]+)/', $this->video_url, $matches)) {
            $this->video_id = $matches[1];
            $this->video_platform = 'youtube';
            $this->fetchYoutubeDuration();
        }
        // Vimeo patterns
        elseif (preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $matches)) {
            $this->video_id = $matches[1];
            $this->video_platform = 'vimeo';
            $this->fetchVimeoDuration();
        }
    }

    public function fetchYoutubeDuration(): void
    {
        if (!$this->video_id || $this->video_platform !== 'youtube') {
            return;
        }

        try {
            // Usando oEmbed API do YouTube para obter informações
            $oembedUrl = "https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={$this->video_id}&format=json";
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (compatible; VideoFetcher/1.0)'
                ]
            ]);
            
            $response = file_get_contents($oembedUrl, false, $context);
            
            if ($response) {
                $data = json_decode($response, true);
                
                // Tentar obter duração de uma página diretamente
                $this->fetchYoutubeDurationFromPage();
            }
        } catch (\Exception $e) {
            Log::warning("Erro ao buscar duração do YouTube: " . $e->getMessage());
        }
    }

    private function fetchYoutubeDurationFromPage(): void
    {
        try {
            $videoUrl = "https://www.youtube.com/watch?v={$this->video_id}";
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (compatible; VideoFetcher/1.0)'
                ]
            ]);
            
            $html = file_get_contents($videoUrl, false, $context);
            
            if ($html) {
                // Procurar por duração na página
                if (preg_match('/"lengthSeconds":"(\d+)"/', $html, $matches)) {
                    $seconds = (int)$matches[1];
                    $this->duration = $this->formatDuration($seconds);
                }
            }
        } catch (\Exception $e) {
            Log::warning("Erro ao buscar duração da página do YouTube: " . $e->getMessage());
        }
    }

    public function fetchVimeoDuration(): void
    {
        if (!$this->video_id || $this->video_platform !== 'vimeo') {
            return;
        }

        try {
            $oembedUrl = "https://vimeo.com/api/oembed.json?url=https://vimeo.com/{$this->video_id}";
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (compatible; VideoFetcher/1.0)'
                ]
            ]);
            
            $response = file_get_contents($oembedUrl, false, $context);
            
            if ($response) {
                $data = json_decode($response, true);
                
                if (isset($data['duration'])) {
                    $this->duration = $this->formatDuration($data['duration']);
                }
            }
        } catch (\Exception $e) {
            Log::warning("Erro ao buscar duração do Vimeo: " . $e->getMessage());
        }
    }

    private function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%d:%02d', $minutes, $seconds);
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
