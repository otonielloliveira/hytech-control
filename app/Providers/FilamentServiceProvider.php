<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Configurar localização para pt_BR
        App::setLocale('pt_BR');
        
        // Registrar CSS customizado para o Filament
        FilamentAsset::register([
            Css::make('filament-custom', resource_path('css/filament-custom.css')),
        ]);
    }
}
