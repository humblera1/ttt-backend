<?php

namespace App\Filament\Actions\Status;

use App\Models\Question;
use App\Services\api\v1\QuestionService;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Auth\Access\AuthorizationException;

class ResetQuestionAction extends ResetAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'reset';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function (): void {
            $result = $this->process(function (Question $record) {
                if (! auth()->user()->can('changeStatus', $record)) {
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
    }
}
