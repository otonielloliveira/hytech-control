<?php

namespace App\Filament\Resources\BlogConfigResource\Pages;

use App\Filament\Resources\BlogConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogConfig extends EditRecord
{
    protected static string $resource = BlogConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
