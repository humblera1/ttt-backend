<?php

namespace App\Filament\Resources\Widgets\Status;

use App\Enums\Status;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class StatusBadge extends Widget
{
    protected static string $view = 'filament.widgets.status.status-badge';

    public ?Model $record = null;

    protected $listeners = [
        'statusUpdated',
    ];

    public function getBadgeColor(): string
    {
        return Status::colorByValue($this->record->status);
    }

    public function getBadgeLabel(): string
    {
        return ucfirst($this->record->status);
    }
}
