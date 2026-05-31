<?php

namespace App\Filament\Actions\Form\Status\Suggestion;

use App\Interfaces\v1\Status\StatusWithReviewInterface;
use App\Models\QuestionPositionSuggestion;
use App\Services\api\v1\Suggestion\QuestionPositionSuggestionService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Resources\Pages\Page;
use Illuminate\Auth\Access\AuthorizationException;

class PositionReturnToReviewAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'return-to-review';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Return to Review'));

        $this->defaultColor('info');

        $this->icon('heroicon-m-pencil');

        $this->requiresConfirmation();

        $this->modalIcon('heroicon-m-pencil');

        $this->modalHeading('Return to Review');

        $this->action(function (): void {
            $result = $this->process(function (QuestionPositionSuggestion $record) {
                if (!auth()->user()->can('changeStatus', $record)) {
                    throw new AuthorizationException('You do not have permission to change status.');
                }

                $service = app(QuestionPositionSuggestionService::class);

                return $service->returnForReviewSuggestion($record);
            });

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });

        $this->visible(function (StatusWithReviewInterface $record) {
            return !$record->isReadyForReview() && auth()->user()->can('changeStatus', $record);
        });

        $this->after(fn (Page $livewire) => $livewire->dispatch('statusUpdated'));
    }
}
