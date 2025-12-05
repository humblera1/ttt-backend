<?php

namespace App\Traits\Models;

use App\Enums\Suggestion\Status;

trait HasStatusWithReview
{
    use WithStatus;

    public function isReadyForReview(): bool
    {
        return $this->status === Status::ReadyForReview->value;
    }
}
