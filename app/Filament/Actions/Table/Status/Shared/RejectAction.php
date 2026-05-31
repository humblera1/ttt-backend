<?php

namespace App\Filament\Actions\Table\Status\Shared;

use App\Interfaces\v1\Status\StatusInterface;
use App\Services\api\v1\StatusService;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

class RejectAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'reject';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-x-mark');

        $this->color('danger');

        $this->hidden(static function (StatusInterface $record): bool {
            return !auth()->user()->can('changeStatus', $record) || $record->isRejected();
        });

        $this->action(function (): void {
            $result = $this->process(function (Model $record) {
                if (!auth()->user()->can('changeStatus', $record)) {
                    throw new AuthorizationException('You do not have permission to change status.');
                }

                $service = app(StatusService::class);

                $service->reject($record);
            });

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });
    }
}
