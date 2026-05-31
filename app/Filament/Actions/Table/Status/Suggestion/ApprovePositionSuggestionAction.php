<?php

namespace App\Filament\Actions\Table\Status\Suggestion;

use App\Models\QuestionPositionSuggestion;
use App\Services\api\v1\Suggestion\QuestionPositionSuggestionService;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Auth\Access\AuthorizationException;

class ApprovePositionSuggestionAction extends ApproveAction
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
            $result = $this->process(function (QuestionPositionSuggestion $record) {
                if (! auth()->user()->can('changeStatus', $record)) {
                    throw new AuthorizationException('You do not have permission to change status.');
                }

                $service = app(QuestionPositionSuggestionService::class);

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
