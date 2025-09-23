<?php

namespace App\Traits\Status;

use App\Enums\Status;

trait HasStatus
{
    public function isApproved(): bool
    {
        return $this->status === Status::Approved->value;
    }
}
