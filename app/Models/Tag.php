<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Posts que possuem esta tag
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tags', 'tag_id', 'post_id')
                    ->withTimestamps();
    }

    /**
     * Posts publicados que possuem esta tag
     */
    public function publishedPosts(): BelongsToMany
    {
        return $this->posts()->where('status', 'published')
                             ->where('published_at', '<=', now());
    }

    /**
     * Scope para buscar por nome
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', '%' . $term . '%');
    }

    /**
     * Scope para tags populares (com mais posts)
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->has('publishedPosts')
                    ->withCount('publishedPosts')
                    ->orderByDesc('published_posts_count')
                    ->take($limit);
    }

    /**
     * Método estático para tags populares
     */
    public static function popular($limit = 10)
    {
        return static::query()->popular($limit)->get();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
