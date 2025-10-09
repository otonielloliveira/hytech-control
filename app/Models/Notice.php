<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notice extends Model
{
    use HasFactory;

    protected $table = 'blog_notices';

    protected $fillable = [
        'title',
        'content',
        'image',
        'link_type',
        'link_url',
        'internal_route',
        'priority',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInDateRange($query)
    {
        $now = Carbon::now();
        return $query->where(function ($q) use ($now) {
            $q->where(function ($subQ) use ($now) {
                $subQ->whereNull('start_date')
                     ->orWhere('start_date', '<=', $now);
            })->where(function ($subQ) use ($now) {
                $subQ->whereNull('end_date')
                     ->orWhere('end_date', '>=', $now);
            });
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('created_at', 'desc');
    }

    // Methods
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getFinalLinkAttribute(): ?string
    {
        switch ($this->link_type) {
            case 'external':
                return $this->link_url;
            case 'internal':
                if ($this->internal_route && $this->link_url) {
                    return route($this->internal_route, $this->link_url);
                } elseif ($this->link_url) {
                    return url($this->link_url);
                }
                return null;
            default:
                return null;
        }
    }

    public function hasValidLink(): bool
    {
        return $this->link_type !== 'none' && !empty($this->final_link);
    }

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }
        
        return true;
    }

    public static function getActiveNotices(int $limit = 4)
    {
        return static::active()
            ->inDateRange()
            ->ordered()
            ->take($limit)
            ->get();
    }
}
