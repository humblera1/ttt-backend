<?php

namespace App\Filament\Infolists\Notification;

use Filament\Infolists\Components\ViewEntry;

class ListDataEntry extends ViewEntry
{
    protected string $view = 'filament.widgets.notification.list-data';

    public static function make(string $name = 'placeholders_list'): static
    {
        return parent::make($name);
    }
}
