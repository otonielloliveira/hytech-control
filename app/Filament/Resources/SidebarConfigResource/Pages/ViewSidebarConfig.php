<?php

namespace App\Filament\Resources\SidebarConfigResource\Pages;

use App\Filament\Resources\SidebarConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSidebarConfig extends ViewRecord
{
    protected static string $resource = SidebarConfigResource::class;
    
    protected static ?string $title = 'Visualizar Widget';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar Widget')
                ->icon('heroicon-o-pencil'),
        ];
    }
}
