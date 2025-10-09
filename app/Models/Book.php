<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $table = 'blog_books';

    protected $fillable = [
        'title',
        'author',
        'description',
        'cover_image',
        'amazon_link',
        'goodreads_link',
        'pdf_link',
        'rating',
        'isbn',
        'publication_year',
        'category',
        'review',
        'is_featured',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc')->orderBy('created_at', 'desc');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return Storage::url($this->cover_image);
        }
        
        // Fallback to a default book cover
        return asset('images/default-book-cover.png');
    }

    public function getFormattedRatingAttribute()
    {
        if (!$this->rating) {
            return 'N/A';
        }
        
        return number_format($this->rating, 1) . '/5.0';
    }

    public function getRatingStarsAttribute()
    {
        if (!$this->rating) {
            return '';
        }
        
        $fullStars = floor($this->rating);
        $halfStar = ($this->rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
        
        $stars = str_repeat('★', $fullStars);
        if ($halfStar) {
            $stars .= '☆';
        }
        $stars .= str_repeat('☆', $emptyStars);
        
        return $stars;
    }

    public function getShortDescriptionAttribute()
    {
        return $this->description ? Str::limit($this->description, 100) : '';
    }

    public function getShortReviewAttribute()
    {
        return $this->review ? Str::limit($this->review, 150) : '';
    }

    public function hasAmazonLink()
    {
        return !empty($this->amazon_link);
    }

    public function hasGoodreadsLink()
    {
        return !empty($this->goodreads_link);
    }

    public function hasPdfLink()
    {
        return !empty($this->pdf_link);
    }

    public static function getFeaturedBooks($limit = 3)
    {
        return self::active()
                   ->featured()
                   ->byPriority()
                   ->limit($limit)
                   ->get();
    }

    public static function getRecentBooks($limit = 5)
    {
        return self::active()
                   ->byPriority()
                   ->limit($limit)
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

    public static function getBooksByCategory($category, $limit = 10)
    {
        return self::active()
                   ->byCategory($category)
                   ->byPriority()
                   ->limit($limit)
                   ->get();
    }
}