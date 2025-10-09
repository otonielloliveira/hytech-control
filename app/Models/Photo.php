<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id',
        'title',
        'description',
        'image_path',
        'thumbnail_path',
        'order',
        'is_featured',
        'file_size',
        'width',
        'height',
        'alt_text',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'order' => 'integer',
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    // Relationships
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    // Scopes
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Accessors
    public function getImageUrlAttribute(): string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : '';
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : $this->image_url;
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '';
        }
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDimensionsAttribute(): string
    {
        if ($this->width && $this->height) {
            return $this->width . ' x ' . $this->height;
        }
        return '';
    }

    // Methods
    public function deleteFiles(): void
    {
        if ($this->image_path) {
            Storage::delete('public/' . $this->image_path);
        }
        
        if ($this->thumbnail_path) {
            Storage::delete('public/' . $this->thumbnail_path);
        }
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($photo) {
            $photo->deleteFiles();
        });

        static::saved(function ($photo) {
            // Update album photo count
            if ($photo->album) {
                $photo->album->updatePhotoCount();
            }
        });

        static::deleted(function ($photo) {
            // Update album photo count
            if ($photo->album) {
                $photo->album->updatePhotoCount();
            }
        });
    }
}
