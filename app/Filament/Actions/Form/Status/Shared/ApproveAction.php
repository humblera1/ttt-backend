<?php

namespace App\Filament\Actions\Form\Status\Shared;

use App\Interfaces\v1\Status\StatusInterface;
use App\Services\api\v1\StatusService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Resources\Pages\Page;
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

        $this->label(__('Approve'));

        $this->defaultColor('success');

        $this->icon('heroicon-m-check');

        $this->requiresConfirmation();

        $this->modalIcon('heroicon-m-check');

        $this->modalHeading('Approve');

        $this->action(function (): void {
            $result = $this->process(function (Model $record) {
                if (!auth()->user()->can('changeStatus', $record)) {
                    throw new AuthorizationException('You do not have permission to change status.');
                }

                $service = app(StatusService::class);

                return $service->approve($record);
            });

            if (!$result) {
                $this->failure();

                return;
            }

            $this->success();
        });

        $this->visible(function (StatusInterface $record) {
            return $record->isPending() && auth()->user()->can('changeStatus', $record);
        });

        $this->after(fn (Page $livewire) => $livewire->dispatch('statusUpdated'));
    }
}
