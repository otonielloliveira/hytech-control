<?php

namespace App\Services;

use App\Models\SidebarConfig;
use App\Models\BlogConfig;

class SidebarService
{
    /**
     * Get all active widgets ordered by priority
     */
    public static function getActiveWidgets(): array
    {
        $config = BlogConfig::current();
        
        if (!$config->show_sidebar) {
            return [];
        }

        $widgets = SidebarConfig::getActiveWidgets();
        $availableWidgets = self::getAvailableWidgets();
        
        $activeWidgets = [];
        
        foreach ($widgets as $widget) {
            if (isset($availableWidgets[$widget->widget_name])) {
                $activeWidgets[] = [
                    'name' => $widget->widget_name,
                    'config' => $widget,
                    'component' => $availableWidgets[$widget->widget_name]['component'],
                    'title' => $availableWidgets[$widget->widget_name]['title'],
                ];
            }
        }
        
        return $activeWidgets;
    }

    /**
     * Get all available widgets definitions
     */
    public static function getAvailableWidgets(): array
    {
        return [
            'notices' => [
                'title' => 'Recados',
                'component' => 'sidebar.notices',
                'description' => 'Exibe recados e avisos importantes',
                'default_active' => true,
                'default_order' => 1,
            ],
            'tags' => [
                'title' => 'Tags',
                'component' => 'sidebar.tags',
                'description' => 'Exibe as tags mais populares do blog',
                'default_active' => true,
                'default_order' => 2,
            ],
            'youtube' => [
                'title' => 'Canal YouTube',
                'component' => 'sidebar.youtube',
                'description' => 'Exibe informações do canal do YouTube',
                'default_active' => false,
                'default_order' => 3,
            ],
            'polls' => [
                'title' => 'Enquetes',
                'component' => 'sidebar.polls',
                'description' => 'Exibe enquetes ativas para votação',
                'default_active' => true,
                'default_order' => 4,
            ],
            'lectures' => [
                'title' => 'Palestras',
                'component' => 'sidebar.lectures',
                'description' => 'Exibe próximas palestras e eventos',
                'default_active' => false,
                'default_order' => 5,
            ],
            'hangouts' => [
                'title' => 'Hangouts',
                'component' => 'sidebar.hangouts',
                'description' => 'Exibe hangouts ativos',
                'default_active' => false,
                'default_order' => 6,
            ],
            'books' => [
                'title' => 'Livros Recomendados',
                'component' => 'sidebar.books',
                'description' => 'Exibe livros e materiais recomendados',
                'default_active' => false,
                'default_order' => 7,
            ],
            'downloads' => [
                'title' => 'Downloads',
                'component' => 'sidebar.downloads',
                'description' => 'Exibe arquivos para download',
                'default_active' => false,
                'default_order' => 8,
            ],
        ];
    }

    /**
     * Initialize default widgets in database
     */
    public static function initializeDefaultWidgets(): void
    {
        $availableWidgets = self::getAvailableWidgets();
        
        foreach ($availableWidgets as $widgetName => $widgetData) {
            SidebarConfig::firstOrCreate(
                ['widget_name' => $widgetName],
                [
                    'is_active' => $widgetData['default_active'],
                    'sort_order' => $widgetData['default_order'],
                    'title_color' => '#1e40af',
                    'background_color' => '#f8fafc',
                    'text_color' => '#1f2937',
                ]
            );
        }
    }

    /**
     * Get sidebar configuration
     */
    public static function getSidebarConfig(): array
    {
        $config = BlogConfig::current();
        
        return [
            'show_sidebar' => $config->show_sidebar,
            'position' => $config->sidebar_position,
            'width' => $config->sidebar_width,
            'default_title_color' => $config->default_widget_title_color,
            'default_background_color' => $config->default_widget_background_color,
            'default_text_color' => $config->default_widget_text_color,
        ];
    }
}