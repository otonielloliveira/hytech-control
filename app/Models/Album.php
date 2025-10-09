<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'cover_image',
        'event_date',
        'location',
        'is_active',
        'priority',
        'photo_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'event_date' => 'date',
        'priority' => 'integer',
        'photo_count' => 'integer',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->orderBy('order');
    }

    public function featuredPhotos(): HasMany
    {
        return $this->hasMany(Photo::class)->where('is_featured', true)->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('event_date', 'desc');
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        
        $firstPhoto = $this->photos()->first();
        return $firstPhoto ? $firstPhoto->image_url : null;
    }

    public function getFormattedEventDateAttribute(): ?string
    {
        return $this->event_date ? $this->event_date->format('d/m/Y') : null;
    }

    public function updatePhotoCount(): void
    {
        $this->update(['photo_count' => $this->photos()->count()]);
    }

    public static function getActiveAlbums(int $limit = 12)
    {
        return static::active()
            ->ordered()
            ->with(['photos' => function ($query) {
                $query->take(4);
            }])
            ->take($limit)
            ->get();
    }
}