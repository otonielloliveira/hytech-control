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
    ];

    protected $casts = [
        'social_links' => 'array',
        'meta_keywords' => 'array',
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
     * Obter URL do logo
     */
    public function getLogoUrlAttribute(): string
    {
        return $this->site_logo ? asset('storage/' . $this->site_logo) : asset('images/logo-default.png');
    }

    /**
     * Obter URL do favicon
     */
    public function getFaviconUrlAttribute(): string
    {
        return $this->site_favicon ? asset('storage/' . $this->site_favicon) : asset('images/favicon-default.ico');
    }
}
