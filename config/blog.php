<?php

return [
    /**
     * ------------------------------------------------------------
     * Tables
     * This is the prefix for all blog tables.
     * ------------------------------------------------------------
     */
    'tables' => [
        'prefix' => 'blog_',
    ],

    /**
     * ------------------------------------------------------------
     * Route
     * This is the route configuration for the blog.
     * ------------------------------------------------------------
     */
    'route' => [
        'prefix' => 'blog',
        'middleware' => ['web'],
        'home' => [
            'name' => 'blog.home',
            'url' => env('APP_URL'),
        ],
    ],

    /**
     * ------------------------------------------------------------
     * User
     * This is the user configuration for the blog.
     * ------------------------------------------------------------
     */
    'user' => [
        'model' => \App\Models\User::class,
        'foreign_key' => 'user_id',
        'columns' => [
            'name' => 'name',
            'email' => 'email',
            'avatar' => 'avatar',
        ],
    ],

    /**
     * ------------------------------------------------------------
     * SEO
     * This is the SEO configuration for the blog.
     * ------------------------------------------------------------
     */
    'seo' => [
        'meta' => [
            'title' => 'HyTech Control Blog',
            'description' => 'Blog oficial da HyTech Control - Tecnologia e Inovação',
            'keywords' => ['tecnologia', 'inovação', 'desenvolvimento', 'laravel', 'filament'],
        ],
    ],

    /**
     * ------------------------------------------------------------
     * Filesystem
     * This is the filesystem configuration for the blog.
     * ------------------------------------------------------------
     */
    'filesystem' => [
        'visibility' => 'public',
        'disk' => 'public',
    ],

    /**
     * ------------------------------------------------------------
     * Pagination
     * ------------------------------------------------------------
     */
    'pagination' => [
        'per_page' => 10,
    ],

    /**
     * ------------------------------------------------------------
     * Comments
     * ------------------------------------------------------------
     */
    'comments' => [
        'enabled' => true,
        'moderation' => true, // Comentários precisam aprovação
        'guest_comments' => false, // Apenas usuários logados
    ],

    /**
     * ------------------------------------------------------------
     * Newsletter
     * ------------------------------------------------------------
     */
    'newsletter' => [
        'enabled' => true,
        'double_optin' => true,
    ],
];