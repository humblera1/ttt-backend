<?php

namespace App\Interfaces\v1\Status;

interface StatusInterface
{
    public function isApproved(): bool;

    public function isRejected(): bool;

    public function isPending(): bool;
}
