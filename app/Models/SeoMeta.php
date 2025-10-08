<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMeta extends Model
{
    use HasFactory;

    protected $table = 'blog_seo_meta';

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'canonical_url',
        'robots',
        'schema_markup',
    ];

    protected $casts = [
        'meta_keywords' => 'array',
        'schema_markup' => 'array',
    ];

    // Relationships
    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    // Methods
    public function getMetaTitle(): string
    {
        return $this->meta_title ?: $this->seoable->title ?? '';
    }

    public function getMetaDescription(): string
    {
        return $this->meta_description ?: $this->seoable->excerpt ?? '';
    }

    public function getOgTitle(): string
    {
        return $this->og_title ?: $this->getMetaTitle();
    }

    public function getOgDescription(): string
    {
        return $this->og_description ?: $this->getMetaDescription();
    }

    public function getTwitterTitle(): string
    {
        return $this->twitter_title ?: $this->getMetaTitle();
    }

    public function getTwitterDescription(): string
    {
        return $this->twitter_description ?: $this->getMetaDescription();
    }
}