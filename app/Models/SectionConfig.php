<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_key',
        'section_name',
        'section_icon',
        'section_description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function posts()
    {
        return $this->hasMany(Post::class, 'destination', 'section_key');
    }

    public function publishedPosts()
    {
        return $this->posts()->published();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Static methods for getting section configurations
    public static function getSectionConfig($key, $default = null)
    {
        $config = static::where('section_key', $key)->where('is_active', true)->first();
        
        if (!$config) {
            return $default ?: [
                'name' => ucfirst(str_replace('_', ' ', $key)),
                'icon' => 'fas fa-folder',
                'description' => ''
            ];
        }

        return [
            'name' => $config->section_name,
            'icon' => $config->section_icon ?: 'fas fa-folder',
            'description' => $config->section_description ?: ''
        ];
    }

    public static function getAllSectionConfigs()
    {
        return static::active()->ordered()->get()->keyBy('section_key');
    }

    // Boot method to set defaults
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sectionConfig) {
            if (is_null($sectionConfig->sort_order)) {
                $sectionConfig->sort_order = static::max('sort_order') + 1;
            }
        });
    }
}