<?php

namespace App\Filament\Actions\Forms\Status;

use App\Interfaces\v1\Status\StatusInterface;
use App\Services\api\v1\StatusService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Resources\Pages\Page;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

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

        $this->modalHeading('Reset to Pending');

        $this->action(function (): void {
            $result = $this->process(function (Model $record) {
                if (!auth()->user()->can('changeStatus', $record)) {
                    throw new AuthorizationException('You do not have permission to change status.');
                }

                $service = app(StatusService::class);

                return $service->reset($record);
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

        $this->after(fn (Page $livewire) => $livewire->dispatch('statusUpdated'));
    }
}
