<?php

namespace App\Filament\Actions\Status;

use App\Interfaces\v1\Status\StatusInterface;
use App\Services\api\v1\StatusService;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\Action;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

class ApproveAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'approve';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-check');

        $this->color('success');

        $this->hidden(static function (StatusInterface $record): bool {
            return !auth()->user()->can('changeStatus', $record) || $record->isApproved();
        });

        $this->action(function (): void {
            $result = $this->process(function (Model $record) {
                if (!auth()->user()->can('changeStatus', $record)) {
                    throw new AuthorizationException('You do not have permission to change status.');
                }

                $service = app(StatusService::class);

                $service->approve($record);
            });

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });
    }
}
