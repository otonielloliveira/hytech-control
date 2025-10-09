<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Hangout extends Model
{
    use HasFactory;

    protected $table = 'blog_hangouts';

    protected $fillable = [
        'title',
        'description',
        'platform',
        'meeting_link',
        'meeting_id',
        'meeting_password',
        'scheduled_at',
        'duration_minutes',
        'max_participants',
        'host_name',
        'host_email',
        'agenda',
        'status',
        'is_public',
        'requires_registration',
        'cover_image',
        'priority',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_public' => 'boolean',
        'requires_registration' => 'boolean',
    ];

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
                     ->where('status', 'scheduled');
    }

    public function scopePast($query)
    {
        return $query->where('scheduled_at', '<', now())
                     ->whereIn('status', ['ended', 'scheduled']);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc')->orderBy('scheduled_at', 'asc');
    }

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return Storage::url($this->cover_image);
        }
        
        // Default cover based on platform
        return match($this->platform) {
            'google-meet' => asset('images/google-meet-default.png'),
            'zoom' => asset('images/zoom-default.png'),
            'teams' => asset('images/teams-default.png'),
            default => asset('images/hangout-default.png')
        };
    }

    public function getPlatformNameAttribute()
    {
        return match($this->platform) {
            'google-meet' => 'Google Meet',
            'zoom' => 'Zoom',
            'teams' => 'Microsoft Teams',
            'discord' => 'Discord',
            'jitsi' => 'Jitsi Meet',
            'webex' => 'Cisco Webex',
            default => ucfirst($this->platform)
        };
    }

    public function getPlatformIconAttribute()
    {
        return match($this->platform) {
            'google-meet' => 'fab fa-google',
            'zoom' => 'fas fa-video',
            'teams' => 'fab fa-microsoft',
            'discord' => 'fab fa-discord',
            'jitsi' => 'fas fa-video',
            'webex' => 'fas fa-video',
            default => 'fas fa-video'
        };
    }

    public function getPlatformColorAttribute()
    {
        return match($this->platform) {
            'google-meet' => '#4285f4',
            'zoom' => '#2d8cff',
            'teams' => '#6264a7',
            'discord' => '#7289da',
            'jitsi' => '#1d76ba',
            'webex' => '#00bceb',
            default => '#6c757d'
        };
    }

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'scheduled' => 'primary',
            'live' => 'success',
            'ended' => 'secondary',
            'cancelled' => 'danger',
            default => 'info'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'scheduled' => 'Agendado',
            'live' => 'Ao Vivo',
            'ended' => 'Finalizado',
            'cancelled' => 'Cancelado',
            default => ucfirst($this->status)
        };
    }

    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}min";
        }
        
        return "{$minutes}min";
    }

    public function getTimeUntilMeetingAttribute()
    {
        if ($this->scheduled_at->isPast()) {
            return null;
        }
        
        return $this->scheduled_at->diffForHumans();
    }

    public function isLive()
    {
        return $this->status === 'live';
    }

    public function isUpcoming()
    {
        return $this->status === 'scheduled' && $this->scheduled_at->isFuture();
    }

    public function isEnded()
    {
        return $this->status === 'ended' || 
               ($this->status === 'scheduled' && $this->scheduled_at->isPast());
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function canJoin()
    {
        return $this->isLive() || 
               ($this->isUpcoming() && $this->scheduled_at->diffInMinutes() <= 15);
    }

    public static function getUpcomingHangouts($limit = 3)
    {
        return self::public()
                   ->upcoming()
                   ->byPriority()
                   ->limit($limit)
                   ->get();
    }

    public static function getLiveHangouts()
    {
        return self::public()
                   ->live()
                   ->byPriority()
                   ->get();
    }

    public static function getRecentHangouts($limit = 5)
    {
        return self::public()
                   ->past()
                   ->orderBy('scheduled_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public function updateStatus()
    {
        $now = now();
        $endTime = $this->scheduled_at->addMinutes($this->duration_minutes);
        
        if ($this->status === 'scheduled') {
            if ($now->between($this->scheduled_at, $endTime)) {
                $this->update(['status' => 'live']);
            } elseif ($now->gt($endTime)) {
                $this->update(['status' => 'ended']);
            }
        } elseif ($this->status === 'live' && $now->gt($endTime)) {
            $this->update(['status' => 'ended']);
        }
    }
}