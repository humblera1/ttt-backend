<?php

namespace App\Filament\Actions\Page\Status\Question;

use App\Filament\Resources\QuestionResource\Pages\EditQuestion;
use App\Interfaces\v1\Status\StatusInterface;
use App\Models\Question;
use App\Services\api\v1\QuestionService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Auth\Access\AuthorizationException;

class ResetAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'reset';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Reset'));

        $this->defaultColor('warning');

        $this->icon('heroicon-m-arrow-path');

        $this->requiresConfirmation();

        $this->modalIcon('heroicon-m-arrow-path');

        $this->modalHeading('Reset Question Status');

        $this->action(function (): void {
            $result = $this->process(function (Question $record) {
                if (!auth()->user()->can('changeStatus', $record)) {
                    throw new AuthorizationException('You do not have permission to change status.');
                }

                $service = app(QuestionService::class);

                return $service->resetQuestion($record);
            });

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });

        $this->visible(function (StatusInterface $record) {
            return !$record->isPending() && auth()->user()->can('changeStatus', $record);
        });

        $this->after(fn (EditQuestion $livewire) => $livewire->dispatch('statusUpdated'));
    }
}
