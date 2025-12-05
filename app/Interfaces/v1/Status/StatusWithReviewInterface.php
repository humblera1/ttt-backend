<?php

namespace App\Interfaces\v1\Status;

interface StatusWithReviewInterface extends StatusInterface
{
    public function isReadyForReview(): bool;
}
