<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\BlogComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register PaymentManager
        $this->app->singleton(\App\Services\PaymentManager::class, function ($app) {
            return new \App\Services\PaymentManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register view composer for blog layout
        View::composer([
            'layouts.blog',
            'blog.*'
        ], BlogComposer::class);
        
        // Register observers
        \App\Models\Video::observe(\App\Observers\VideoObserver::class);
        
        // Register event listeners
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\UpdateLastLogin::class,
        );
    }
}
