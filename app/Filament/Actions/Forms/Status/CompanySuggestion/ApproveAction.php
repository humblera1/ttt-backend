<?php

namespace App\Filament\Actions\Forms\Status\CompanySuggestion;

use App\Filament\Actions\Forms\Status\ApproveAction as BaseApproveAction;
use App\Interfaces\v1\Status\StatusWithReviewInterface;
use App\Models\QuestionCompanySuggestion;
use App\Services\api\v1\Suggestion\QuestionCompanySuggestionService;
use Illuminate\Auth\Access\AuthorizationException;

class ApproveAction extends BaseApproveAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function (): void {
            $result = $this->process(function (QuestionCompanySuggestion $record) {
                if (!auth()->user()->can('changeStatus', $record)) {
                    throw new AuthorizationException('You do not have permission to change status.');
                }

                $service = app(QuestionCompanySuggestionService::class);

                return $service->approveSuggestion($record);
            });

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });

        $this->visible(function (StatusWithReviewInterface $record) {
            return ($record->isPending() || $record->isReadyForReview())
                && auth()->user()->can('changeStatus', $record);
        });
    }
}
