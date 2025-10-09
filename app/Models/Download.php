<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Download extends Model
{
    use HasFactory;

    protected $table = 'blog_downloads';

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'icon',
        'download_count',
        'is_active',
        'priority',
        'requires_login',
        'category',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_login' => 'boolean',
        'download_count' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc')->orderBy('created_at', 'desc');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->where('download_count', '>', 0)
                    ->orderBy('download_count', 'desc')
                    ->take($limit);
    }

    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = floatval($this->file_size);
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }

    public function getIconClassAttribute()
    {
        if ($this->icon) {
            return $this->icon;
        }

        // Auto-detect icon based on file type
        $type = strtolower($this->file_type ?? '');
        
        return match($type) {
            'pdf' => 'fas fa-file-pdf',
            'doc', 'docx' => 'fas fa-file-word',
            'xls', 'xlsx' => 'fas fa-file-excel',
            'ppt', 'pptx' => 'fas fa-file-powerpoint',
            'zip', 'rar', '7z' => 'fas fa-file-archive',
            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image',
            'mp4', 'avi', 'mov' => 'fas fa-file-video',
            'mp3', 'wav', 'ogg' => 'fas fa-file-audio',
            'txt' => 'fas fa-file-alt',
            default => 'fas fa-download'
        };
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public static function getActiveDownloads($limit = 5)
    {
        return self::active()
                   ->byPriority()
                   ->limit($limit)
                   ->get();
    }

    public static function popular($limit = 3)
    {
        return self::active()
                   ->popular($limit)
                   ->get();
    }

    public static function getCategories()
    {
        return self::active()
                   ->distinct()
                   ->pluck('category')
                   ->filter()
                   ->sort()
                   ->values()
                   ->toArray();
    }
}