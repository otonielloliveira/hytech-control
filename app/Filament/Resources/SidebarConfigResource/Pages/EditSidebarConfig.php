<?php

namespace App\Filament\Resources\SidebarConfigResource\Pages;

use App\Filament\Resources\SidebarConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSidebarConfig extends EditRecord
{
    protected static string $resource = SidebarConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
