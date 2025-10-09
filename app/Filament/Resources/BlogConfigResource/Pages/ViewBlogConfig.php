<?php

namespace App\Filament\Resources\BlogConfigResource\Pages;

use App\Filament\Resources\BlogConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBlogConfig extends ViewRecord
{
    protected static string $resource = BlogConfigResource::class;
    
    protected static ?string $title = 'Visualizar Configuração do Blog';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar Configuração do Blog')
                ->icon('heroicon-o-pencil'),
        ];
    }
}
