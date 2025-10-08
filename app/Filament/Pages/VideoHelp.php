<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class VideoHelp extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static string $view = 'filament.pages.video-help';
    
    protected static ?string $navigationLabel = 'Ajuda - Vídeos';
    
    protected static ?string $title = 'Como Adicionar Vídeos aos Posts';
    
    protected static ?string $navigationGroup = 'Ajuda';
    
    protected static ?int $navigationSort = 10;
}
