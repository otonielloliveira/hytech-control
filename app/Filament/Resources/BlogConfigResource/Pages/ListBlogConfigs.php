<?php

namespace App\Filament\Resources\BlogConfigResource\Pages;

use App\Filament\Resources\BlogConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlogConfigs extends ListRecords
{
    protected static string $resource = BlogConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
