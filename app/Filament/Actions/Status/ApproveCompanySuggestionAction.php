<?php

namespace App\Filament\Actions\Status;

use App\Models\QuestionCompanySuggestion;
use App\Services\api\v1\Suggestion\QuestionCompanySuggestionService;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Auth\Access\AuthorizationException;

class ApproveCompanySuggestionAction extends ApproveAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'approve';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function (): void {
            $result = $this->process(function (QuestionCompanySuggestion $record) {
                if (! auth()->user()->can('changeStatus', $record)) {
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
    }
}
