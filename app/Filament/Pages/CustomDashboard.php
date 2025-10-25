<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class CustomDashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.pages.dashboard';
    
    public function getTitle(): string
    {
        return 'Dashboard';
    }
}
