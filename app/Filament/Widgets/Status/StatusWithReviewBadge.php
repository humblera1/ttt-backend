<?php

namespace App\Filament\Widgets\Status;

use App\Enums\Suggestion\Status;

class StatusWithReviewBadge extends StatusBadge
{
    public function getBadgeColor(): string
    {
        return Status::colorByValue($this->record->status);
    }
}
