<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollVote extends Model
{
    use HasFactory;

    protected $table = 'blog_poll_votes';

    protected $fillable = [
        'poll_option_id',
        'ip_address',
        'user_agent',
        'voted_at',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    public function pollOption(): BelongsTo
    {
        return $this->belongsTo(PollOption::class);
    }

    public function poll()
    {
        return $this->pollOption->poll();
    }

    public static function hasVotedInPoll($pollId, $ipAddress)
    {
        return self::whereHas('pollOption', function ($query) use ($pollId) {
            $query->where('poll_id', $pollId);
        })->where('ip_address', $ipAddress)->exists();
    }
}