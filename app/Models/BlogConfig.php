<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'site_description',
        'site_logo',
        'site_favicon',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'footer_text',
        'social_links',
        'contact_email',
        'contact_phone',
        'address',
        'google_analytics_id',
        'facebook_pixel_id',
        'custom_head_code',
        'custom_footer_code',
        // Sidebar fields
        'youtube_channel_url',
        'youtube_channel_name',
        'show_youtube_widget',
        'show_sidebar',
        'sidebar_position',
        'sidebar_width',
        'default_widget_title_color',
        'default_widget_background_color',
        'default_widget_text_color',
    ];

    protected $casts = [
        'meta_keywords' => 'array',
        'social_links' => 'array',
        'show_youtube_widget' => 'boolean',
        'show_sidebar' => 'boolean',
    ];

    /**
     * Método singleton para obter a configuração atual
     */
    public static function current(): self
    {
        $config = self::first();
        
        if (!$config) {
            $config = self::create([
                'site_name' => 'HyTech Control Blog',
                'site_description' => 'Blog oficial da HyTech Control - Tecnologia e Inovação',
                'meta_title' => 'HyTech Control Blog',
                'meta_description' => 'Blog oficial da HyTech Control - Tecnologia e Inovação',
                'footer_text' => '© ' . date('Y') . ' HyTech Control. Todos os direitos reservados.',
                'social_links' => [
                    'facebook' => '',
                    'twitter' => '',
                    'instagram' => '',
                    'linkedin' => '',
                    'youtube' => '',
                ],
                'contact_email' => 'contato@hytech.com',
            ]);
        }
        
        return $config;
    }

    /**
     * Atualizar configuração singleton
     */
    public static function updateConfig(array $data): self
    {
        $config = self::current();
        $config->update($data);
        return $config;
    }

        /**
     * Get the URL for the site logo
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->site_logo ? asset('storage/' . $this->site_logo) : null;
    }

    /**
     * Get the URL for the site favicon
     */
    public function getFaviconUrlAttribute(): ?string
    {
        return $this->site_favicon ? asset('storage/' . $this->site_favicon) : null;
    }

    /**
     * Get social media URLs
     */
    public function getFacebookUrlAttribute(): ?string
    {
        return $this->social_links['facebook'] ?? null;
    }

    public function getInstagramUrlAttribute(): ?string
    {
        return $this->social_links['instagram'] ?? null;
    }

    public function getTwitterUrlAttribute(): ?string
    {
        return $this->social_links['twitter'] ?? null;
    }

    public function getYoutubeUrlAttribute(): ?string
    {
        return $this->social_links['youtube'] ?? null;
    }

    public function getLinkedinUrlAttribute(): ?string
    {
        return $this->social_links['linkedin'] ?? null;
    }

    /**
     * Get configuration settings
     */
    public function getAllowCommentsAttribute(): bool
    {
        return true; // Default value
    }

    public function getModerateCommentsAttribute(): bool
    {
        return true; // Default value
    }

    public function getPostsPerPageAttribute(): int
    {
        return 12; // Default value
    }
}
