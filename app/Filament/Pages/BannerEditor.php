<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Banner;

class BannerEditor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static string $view = 'filament.pages.banner-editor';
    
    protected static ?string $navigationLabel = 'Editor de Banners';
    
    protected static ?string $title = 'Editor Visual de Banners';
    
    protected static ?string $navigationGroup = 'Conteúdo';
    
    protected static ?int $navigationSort = 2;

    public ?Banner $record = null;
    
    public function mount(?int $banner = null): void
    {
        if ($banner) {
            $this->record = Banner::findOrFail($banner);
        } else {
            $this->record = new Banner();
        }
    }
}
