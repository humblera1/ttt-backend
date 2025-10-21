<?php

namespace App\Filament\Actions\Question;

use App\Filament\Resources\QuestionResource\Pages\EditQuestion;
use App\Interfaces\v1\Status\StatusInterface;
use App\Models\Question;
use App\Services\api\v1\QuestionService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Auth\Access\AuthorizationException;

class RejectDuplicateQuestionAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'reject-duplicate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Reject as Duplicate'));

        $this->defaultColor('danger');

        $this->icon('heroicon-m-document-duplicate');

        $this->requiresConfirmation();

        $this->modalIcon('heroicon-m-document-duplicate');

        $this->modalHeading('Reject Question as Duplicate');

        $this->form($this->getFormForModal());

        $this->action(function (): void {
            $result = $this->process(function (Question $record, array $data) {
                if (!auth()->user()->can('changeStatus', $record)) {
                    throw new AuthorizationException('You do not have permission to change status.');
                }

                $service = app(QuestionService::class);

                return $service->rejectQuestionAsDuplicate(
                    $record,
                    $data['original_question_id'],
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

        $this->after(function (EditQuestion $livewire) {
            $livewire->dispatch('statusUpdated');

            $livewire->dispatch('statisticsUpdated');
        });
    }

    private function getFormForModal(): array
    {
        return [
            Select::make('original_question_id')
                ->label(__('Original Question'))
                ->required()
                ->searchable()
                ->preload()
                ->options(fn (): array => $this->getOptionsForSelect())
                ->getSearchResultsUsing(fn (string $search): array => $this->getOptionsForSelect($search)),
            Textarea::make('rejection_comment')
                ->label(__('Comment'))
                ->rows(3)
                ->placeholder(__('Optional comment')),
        ];
    }

    private function getOptionsForSelect(?string $search = null): array
    {
        $query = Question::query()
            ->where('id', '!=', $this->record->id)
            ->orderBy('title')
            ->limit(10);

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->pluck('title', 'id')
            ->toArray();
    }
}
