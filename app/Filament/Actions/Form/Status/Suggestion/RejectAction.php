<?php

namespace App\Filament\Actions\Form\Status\Suggestion;

use App\Filament\Actions\Form\Status\Shared\RejectAction as BaseRejectAction;
use App\Interfaces\v1\Status\StatusWithReviewInterface;

class RejectAction extends BaseRejectAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(function (StatusWithReviewInterface $record) {
            return ($record->isPending() || $record->isReadyForReview())
                && auth()->user()->can('changeStatus', $record);
        });
    }
}
