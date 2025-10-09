<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    use HasFactory;

    protected $table = 'blog_polls';

    protected $fillable = [
        'title',
        'description', 
        'is_active',
        'expires_at',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    protected static function booted()
    {
        // Quando uma enquete for ativada, desativar todas as outras
        static::updating(function ($poll) {
            if ($poll->is_active && $poll->isDirty('is_active')) {
                static::where('id', '!=', $poll->id)->update(['is_active' => false]);
            }
        });

        static::creating(function ($poll) {
            if ($poll->is_active) {
                static::query()->update(['is_active' => false]);
            }
        });
    }

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc')->orderBy('created_at', 'desc');
    }

    public function getTotalVotesAttribute()
    {
        return $this->options->sum('votes_count');
    }

    public function hasExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canVote()
    {
        return $this->is_active && !$this->hasExpired();
    }
}