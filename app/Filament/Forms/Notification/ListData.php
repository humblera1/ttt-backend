<?php

namespace App\Filament\Forms\Notification;

use Filament\Forms\Components\ViewField;

class ListData extends ViewField
{
    protected string $view = 'filament.widgets.notification.list-data';

    public static function make(string $name = 'placeholders_list'): static
    {
        return parent::make($name);
    }
}
