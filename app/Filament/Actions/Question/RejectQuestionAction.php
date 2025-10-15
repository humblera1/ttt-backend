<?php

namespace App\Filament\Actions\Question;

use App\Enums\QuestionRejectionReason;
use App\Filament\Resources\QuestionResource\Pages\EditQuestion;
use App\Interfaces\v1\Status\StatusInterface;
use App\Models\Question;
use App\Services\api\v1\QuestionService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Auth\Access\AuthorizationException;

class RejectQuestionAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'reject';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Reject'));

        $this->defaultColor('danger');

        $this->icon('heroicon-m-x-mark');

        $this->requiresConfirmation();

        $this->modalIcon('heroicon-m-x-mark');

        $this->modalHeading('Reject Question');

        $this->form($this->getFormForModal());

        $this->action(function (): void {
            $result = $this->process(function (Question $record, array $data) {
                if (!auth()->user()->can('changeStatus', $record)) {
                    throw new AuthorizationException('You do not have permission to change status.');
                }

                $service = app(QuestionService::class);

                return $service->rejectQuestion(
                    $record,
                    $data['rejection_reason'],
                    $data['rejection_comment'],
                );
            });

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });

        $this->visible(function (StatusInterface $record) {
            return $record->isPending() && auth()->user()->can('changeStatus', $record);
        });

        $this->after(fn (EditQuestion $livewire) => $livewire->dispatch('statusUpdated'));
    }

    private function getFormForModal(): array
    {
        return [
            Select::make('rejection_reason')
                ->label(__('Reason'))
                ->options(QuestionRejectionReason::options()),
            Textarea::make('rejection_comment')
                ->label(__('Comment'))
                ->rows(3)
                ->placeholder(__('Optional comment')),
        ];
    }
}
