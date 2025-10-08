<?php

namespace App\Filament\Resources\BlogConfigResource\Pages;

use App\Filament\Resources\BlogConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogConfig extends CreateRecord
{
    protected static string $resource = BlogConfigResource::class;
}
