<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\View\Composers\ConfigComposer;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Compartilhar $config com todas as views do blog
        View::composer([
            'layouts.blog',
            'blog.*',
            'client.*'
        ], ConfigComposer::class);
    }
}
