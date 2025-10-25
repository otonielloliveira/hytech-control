<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Registrar CSS customizado para o Filament
        FilamentAsset::register([
            Css::make('filament-custom', resource_path('css/filament-custom.css')),
        ]);
    }
}
