<?php

namespace App\Filament\Resources\SidebarConfigResource\Pages;

use App\Filament\Resources\SidebarConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSidebarConfigs extends ListRecords
{
    protected static string $resource = SidebarConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
            //     ->label('Novo Widget')
            //     ->icon('heroicon-o-plus')
            //     ->button(),
        ];
    }
}
