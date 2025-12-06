<?php

namespace App\Filament\Actions\Forms\Status\CompanySuggestion;

use App\Filament\Actions\Forms\Status\RejectAction as BaseRejectAction;
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
