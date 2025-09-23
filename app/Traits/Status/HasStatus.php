<?php

namespace App\Traits\Status;

use App\Enums\Status;

trait HasStatus
{
    public function isApproved(): bool
    {
        return $this->status === Status::Approved->value;
    }

    public function isRejected(): bool
    {
        return $this->status === Status::Rejected->value;
    }

    public function isPending(): bool
    {
        return $this->status === Status::Pending->value;
    }
}
