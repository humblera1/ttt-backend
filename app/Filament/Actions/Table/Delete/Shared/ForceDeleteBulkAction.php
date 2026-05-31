<?php

namespace App\Filament\Actions\Table\Delete\Shared;

use App\Services\api\v1\BulkDeleteService;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;

class ForceDeleteBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    protected string $modelClass;

    public static function getDefaultName(): ?string
    {
        return 'bulk-force-delete';
    }

    public function model(string $modelClass): static
    {
        $this->modelClass = $modelClass;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->requiresConfirmation();

        $this->label(__('Force Delete selected'));

        $this->modalHeading(fn (): string => __('filament-actions::delete.multiple.modal.heading', ['label' => $this->getPluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::delete.multiple.modal.actions.delete.label'));

        $this->successNotificationTitle(__('filament-actions::delete.multiple.notifications.deleted.title'));

        $this->defaultColor('danger');

        $this->icon(FilamentIcon::resolve('actions::delete-action') ?? 'heroicon-m-x-mark');

        $this->modalIcon(FilamentIcon::resolve('actions::delete-action.modal') ?? 'heroicon-o-x-mark');

        $this->action(function (): void {
            if (auth()->user()->cannot('bulkForceDelete', $this->modelClass)) {
                throw new AuthorizationException('You do not have permission to bulk delete records.');
            }

            $this->process(function (Collection $records) {
                $service = app(BulkDeleteService::class, [
                    'modelClass' => $this->modelClass,
                ]);

                $service->forceDeleteMany($records);
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();

        $this->hidden(function (): bool {
            return auth()->user()->cannot('bulkForceDelete', $this->modelClass);
        });
    }

}
