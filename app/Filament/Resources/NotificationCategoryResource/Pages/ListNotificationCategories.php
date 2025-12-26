<?php

namespace App\Filament\Resources\NotificationCategoryResource\Pages;

use App\Filament\Resources\NotificationCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotificationCategories extends ListRecords
{
    protected static string $resource = NotificationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
