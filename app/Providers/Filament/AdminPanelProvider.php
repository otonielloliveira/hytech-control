<?php

namespace App\Providers\Filament;

use Andreia\FilamentNordTheme\FilamentNordThemePlugin;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Rupadana\ApiService\ApiServicePlugin;
use App\Models\BlogConfig;

class AdminPanelProvider extends PanelProvider
{
    private function getLoginImageUrl(): string
    {
        try {
            $config = BlogConfig::current();
            return asset($config->login_image_url ?? '/images/default-login-bg.jpg');
        } catch (\Exception $e) {
            // Return default image if BlogConfig table doesn't exist yet (during migrations)
            return asset('/images/default-login-bg.jpg');
        }
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->darkMode(false)
            ->brandName('Admin Foro do Brasil')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverviewWidget::class,
                \App\Filament\Widgets\RecentActivityWidget::class,
                \App\Filament\Widgets\PopularContentWidget::class,
                \App\Filament\Widgets\QuickActionsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                AuthUIEnhancerPlugin::make() //Plugin permitindo customização da UI de autenticação do Filament Admin
                    ->mobileFormPanelPosition('bottom')
                    ->formPanelWidth('50%')
                    ->formPanelBackgroundColor(Color::Zinc, '300')
                    ->emptyPanelBackgroundImageOpacity('70%')
                    ->emptyPanelBackgroundImageUrl($this->getLoginImageUrl())
                    ->formPanelPosition('right'),
                ApiServicePlugin::make(), //Plugin que cria o serviço de API RESTful para o painel admin do Filament
                FilamentNordThemePlugin::make(), //Plugin que aplica o tema Nord ao painel admin do Filament
            ])->viteTheme('resources/css/filament/admin/theme.css'); //Não alterar essa linha
    }
}
