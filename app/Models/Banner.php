<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image',
        'link_type',
        'link_url',
        'post_id',
        'button_text',
        'target_blank',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'target_blank' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    // Accessors
    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/banner-placeholder.jpg');
    }

    public function getLinkUrlAttribute($value): string
    {
        if ($this->link_type === 'post' && $this->post) {
            return route('blog.post.show', $this->post->slug);
        }
        
        return $value ?: '#';
    }

    public function getTargetAttribute(): string
    {
        return $this->target_blank ? '_blank' : '_self';
    }
}
