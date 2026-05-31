<?php

namespace App\Filament\Actions\Table\Status\Suggestion;

use App\Interfaces\v1\Status\StatusWithReviewInterface;
use App\Models\QuestionCompanySuggestion;
use App\Services\api\v1\Suggestion\QuestionCompanySuggestionService;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\Action;
use Illuminate\Auth\Access\AuthorizationException;

class ReturnCompanyAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'return for review';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-pencil');

        $this->color('info');

        $this->hidden(static function (StatusWithReviewInterface $record): bool {
            return !auth()->user()->can('changeStatus', $record) || $record->isReadyForReview();
        });

        $this->action(function (): void {
            $result = $this->process(function (QuestionCompanySuggestion $record) {
                if (!auth()->user()->can('changeStatus', $record)) {
                    throw new AuthorizationException('You do not have permission to change status.');
                }

                $service = app(QuestionCompanySuggestionService::class);

                $service->returnForReviewSuggestion($record);
            });

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });
    }
}
