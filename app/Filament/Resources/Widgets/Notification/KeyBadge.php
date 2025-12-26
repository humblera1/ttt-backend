<?php

namespace App\Filament\Resources\Widgets\Notification;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class KeyBadge extends Widget
{
    protected static string $view = 'filament.widgets.notification.key-badge';

    public ?Model $record = null;

    public function getBadgeLabel(): string
    {
        return strtolower($this->record->key);
    }
}
