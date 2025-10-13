<?php

namespace App\Filament\Widgets\Status;

use App\Enums\Status;
use Filament\Widgets\Widget;

class StatusBadge extends Widget
{
    protected static string $view = 'filament.widgets.status-badge';

    public string $status;

    public function getBadgeColor(): string
    {
        return Status::colorByValue($this->status);
    }

    public function getBadgeLabel(): string
    {
        return ucfirst($this->status);
    }
}
