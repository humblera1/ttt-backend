<?php

namespace App\Traits\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait WithStatus
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

    #[Scope]
    protected function approved(Builder $query): void
    {
        $query->where('status', Status::Approved->value);
    }
}
