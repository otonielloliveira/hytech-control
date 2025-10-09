<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollOption extends Model
{
    use HasFactory;

    protected $table = 'blog_poll_options';

    protected $fillable = [
        'poll_id',
        'option_text',
        'votes_count',
        'priority',
    ];

    protected $casts = [
        'votes_count' => 'integer',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc')->orderBy('created_at', 'asc');
    }

    public function addVote($ipAddress, $userAgent = null)
    {
        // Verifica se já votou
        $existingVote = $this->votes()
            ->where('ip_address', $ipAddress)
            ->first();

        if ($existingVote) {
            return false; // Já votou
        }

        // Adiciona o voto
        $this->votes()->create([
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'voted_at' => now(),
        ]);

        // Incrementa contador
        $this->increment('votes_count');

        return true;
    }

    public function getVotePercentageAttribute()
    {
        $totalVotes = $this->poll->total_votes;
        
        if ($totalVotes == 0) {
            return 0;
        }

        return round(($this->votes_count / $totalVotes) * 100, 1);
    }
}