<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarConfig extends Model
{
    use HasFactory;

    protected $table = 'blog_sidebar_configs';

    protected $fillable = [
        'widget_name',
        'is_active',
        'sort_order',
        'title_color',
        'background_color',
        'text_color',
        'custom_css',
        'widget_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'widget_settings' => 'array',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('widget_name');
    }

    // Methods
    public static function getActiveWidgets()
    {
        return static::active()->ordered()->get();
    }

    public static function getWidgetConfig(string $widgetName)
    {
        return static::where('widget_name', $widgetName)->first();
    }

    public function getSettingValue(string $key, $default = null)
    {
        return data_get($this->widget_settings, $key, $default);
    }

    public function updateSetting(string $key, $value)
    {
        $settings = $this->widget_settings ?? [];
        data_set($settings, $key, $value);
        $this->update(['widget_settings' => $settings]);
    }
}
