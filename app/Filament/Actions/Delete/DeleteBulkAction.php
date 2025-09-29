<?php

namespace App\Filament\Actions\Delete;

use App\Services\api\v1\BulkDeleteService;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;

class DeleteBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    protected string $modelClass;

    public static function getDefaultName(): ?string
    {
        return 'delete';
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

        $this->label(__('filament-actions::delete.multiple.label'));

        $this->modalHeading(fn (): string => __('filament-actions::delete.multiple.modal.heading', ['label' => $this->getPluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::delete.multiple.modal.actions.delete.label'));

        $this->successNotificationTitle(__('filament-actions::delete.multiple.notifications.deleted.title'));

        $this->defaultColor('danger');

        $this->icon(FilamentIcon::resolve('actions::delete-action') ?? 'heroicon-m-trash');

        $this->modalIcon(FilamentIcon::resolve('actions::delete-action.modal') ?? 'heroicon-o-trash');

        $this->action(function (): void {
            if (auth()->user()->cannot('bulkDelete', $this->modelClass)) {
                throw new AuthorizationException('You do not have permission to bulk delete records.');
            }

            $this->process(function (Collection $records) {
                $service = app(BulkDeleteService::class, [
                    'modelClass' => $this->modelClass,
                ]);

                $service->deleteMany($records);
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();

        $this->hidden(function (HasTable $livewire): bool {
            if (auth()->user()->cannot('bulkDelete', $this->modelClass)) {
                return true;
            }

            $trashedFilterState = $livewire->getTableFilterState(TrashedFilter::class) ?? [];

            if (! array_key_exists('value', $trashedFilterState)) {
                return false;
            }

            if ($trashedFilterState['value']) {
                return false;
            }

            return filled($trashedFilterState['value']);
        });
    }
}
