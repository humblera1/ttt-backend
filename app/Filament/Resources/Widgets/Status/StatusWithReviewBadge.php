<?php

namespace App\Filament\Resources\Widgets\Status;

use App\Enums\Suggestion\Status;

class StatusWithReviewBadge extends StatusBadge
{
    public function getBadgeColor(): string
    {
        return Status::colorByValue($this->record->status);
    }
}
