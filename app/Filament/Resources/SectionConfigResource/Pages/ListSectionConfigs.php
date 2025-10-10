<?php

namespace App\Filament\Resources\SectionConfigResource\Pages;

use App\Filament\Resources\SectionConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSectionConfigs extends ListRecords
{
    protected static string $resource = SectionConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
