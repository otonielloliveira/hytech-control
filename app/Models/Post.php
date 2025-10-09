<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blog_posts';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'video_type',
        'video_url',
        'video_embed_code',
        'show_video_in_content',
        'status',
        'destination',
        'petition_videos',
        'whatsapp_groups',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'user_id',
        'category_id',
        'views_count',
        'is_featured',
        'tags',
        'reading_time',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'show_video_in_content' => 'boolean',
        'tags' => 'array',
        'meta_keywords' => 'array',
        'petition_videos' => 'array',
        'whatsapp_groups' => 'array',
        'views_count' => 'integer',
        'reading_time' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            
            if (empty($post->reading_time)) {
                $post->reading_time = ceil(str_word_count(strip_tags($post->content)) / 200);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            
            if ($post->isDirty('content')) {
                $post->reading_time = ceil(str_word_count(strip_tags($post->content)) / 200);
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->comments()->where('status', 'approved');
    }

    public function seoMeta(): MorphMany
    {
        return $this->morphMany(SeoMeta::class, 'seoable');
    }

    /**
     * Tags relacionadas ao post
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags', 'post_id', 'tag_id')
                    ->withTimestamps();
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categorySlug)
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    // Accessors
    public function getExcerptAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return Str::limit(strip_tags($this->content), 160);
    }

    public function getReadingTimeAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return ceil(str_word_count(strip_tags($this->content)) / 200);
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get video embed HTML
     */
    public function getVideoEmbedAttribute(): ?string
    {
        if ($this->video_type === 'none' || empty($this->video_url)) {
            return null;
        }

        switch ($this->video_type) {
            case 'youtube':
                return $this->getYouTubeEmbed();
            case 'vimeo':
                return $this->getVimeoEmbed();
            case 'custom':
                return $this->video_embed_code;
            default:
                return null;
        }
    }

    /**
     * Get YouTube embed HTML
     */
    private function getYouTubeEmbed(): string
    {
        $videoId = $this->extractYouTubeId($this->video_url);
        if (!$videoId) {
            return '';
        }

        return '<div class="video-container">
                    <iframe width="100%" height="400" 
                            src="https://www.youtube.com/embed/' . $videoId . '" 
                            title="' . htmlspecialchars($this->title) . '"
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                </div>';
    }

    /**
     * Get Vimeo embed HTML
     */
    private function getVimeoEmbed(): string
    {
        $videoId = $this->extractVimeoId($this->video_url);
        if (!$videoId) {
            return '';
        }

        return '<div class="video-container">
                    <iframe src="https://player.vimeo.com/video/' . $videoId . '" 
                            width="100%" height="400" 
                            frameborder="0" 
                            allow="autoplay; fullscreen; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                </div>';
    }

    /**
     * Extract YouTube video ID from URL
     */
    private function extractYouTubeId(string $url): ?string
    {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Extract Vimeo video ID from URL
     */
    private function extractVimeoId(string $url): ?string
    {
        preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|)(\d+)(?:|\/\?)/', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Process content with video shortcodes
     */
    public function getProcessedContentAttribute(): string
    {
        $content = $this->content;
        
        // Processar shortcode [video url="..."]
        $content = preg_replace_callback(
            '/\[video\s+url="([^"]+)"\s*\]/i',
            function ($matches) {
                $url = $matches[1];
                return $this->processVideoShortcode($url);
            },
            $content
        );
        
        // Processar shortcode [youtube id="..."]
        $content = preg_replace_callback(
            '/\[youtube\s+id="([^"]+)"\s*\]/i',
            function ($matches) {
                $videoId = $matches[1];
                return $this->generateYouTubeEmbed($videoId);
            },
            $content
        );
        
        // Processar shortcode [vimeo id="..."]
        $content = preg_replace_callback(
            '/\[vimeo\s+id="([^"]+)"\s*\]/i',
            function ($matches) {
                $videoId = $matches[1];
                return $this->generateVimeoEmbed($videoId);
            },
            $content
        );
        
        return $content;
    }

    /**
     * Process video shortcode with URL detection
     */
    private function processVideoShortcode(string $url): string
    {
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            $videoId = $this->extractYouTubeId($url);
            return $videoId ? $this->generateYouTubeEmbed($videoId) : $url;
        }
        
        if (strpos($url, 'vimeo.com') !== false) {
            $videoId = $this->extractVimeoId($url);
            return $videoId ? $this->generateVimeoEmbed($videoId) : $url;
        }
        
        return $url; // Return original URL if not recognized
    }

    /**
     * Generate YouTube embed HTML
     */
    private function generateYouTubeEmbed(string $videoId): string
    {
        return '<div class="video-container">
                    <iframe width="100%" height="400" 
                            src="https://www.youtube.com/embed/' . $videoId . '" 
                            title="YouTube video"
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                </div>';
    }

    /**
     * Generate Vimeo embed HTML
     */
    private function generateVimeoEmbed(string $videoId): string
    {
        return '<div class="video-container">
                    <iframe src="https://player.vimeo.com/video/' . $videoId . '" 
                            width="100%" height="400" 
                            frameborder="0" 
                            allow="autoplay; fullscreen; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                </div>';
    }

    public function getUrl(): string
    {
        return route('blog.post.show', $this->slug);
    }
    
    // Métodos para destinos das postagens
    public static function getDestinationOptions(): array
    {
        return [
            'artigos' => 'Artigos',
            'peticoes' => 'Petições',
            'ultimas_noticias' => 'Últimas Notícias',
            'noticias_mundiais' => 'Notícias Mundiais',
            'noticias_nacionais' => 'Notícias Nacionais',
            'noticias_regionais' => 'Notícias Regionais',
            'politica' => 'Política',
            'economia' => 'Economia',
        ];
    }
    
    public function getDestinationLabelAttribute(): string
    {
        $options = self::getDestinationOptions();
        return $options[$this->destination] ?? 'Não definido';
    }
    
    // Scope para diferentes destinos
    public function scopeArtigos($query)
    {
        return $query->where('destination', 'artigos');
    }
    
    public function scopePeticoes($query)
    {
        return $query->where('destination', 'peticoes');
    }
    
    public function scopeUltimasNoticias($query)
    {
        return $query->where('destination', 'ultimas_noticias');
    }
    
    public function scopeNoticiasMundiais($query)
    {
        return $query->where('destination', 'noticias_mundiais');
    }
    
    public function scopeNoticiasNacionais($query)
    {
        return $query->where('destination', 'noticias_nacionais');
    }
    
    public function scopeNoticiasRegionais($query)
    {
        return $query->where('destination', 'noticias_regionais');
    }
    
    public function scopePolitica($query)
    {
        return $query->where('destination', 'politica');
    }
    
    public function scopeEconomia($query)
    {
        return $query->where('destination', 'economia');
    }
    
    public function isPetition(): bool
    {
        return $this->destination === 'peticoes';
    }
}