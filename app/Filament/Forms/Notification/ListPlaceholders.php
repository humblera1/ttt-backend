<?php

namespace App\Filament\Forms\Notification;

use Filament\Forms\Components\ViewField;

class ListPlaceholders extends ViewField
{
    protected string $view = 'filament.widgets.notification.list-placeholders';

    public static function make(string $name = 'placeholders_list'): static
    {
        return parent::make($name);
    }

    public function test(): string
    {
        return 'test';
    }
}
