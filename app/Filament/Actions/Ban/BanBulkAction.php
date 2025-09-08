<?php

namespace App\Filament\Actions\Ban;

use App\Services\api\v1\UserBanService;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;

class BanBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'ban';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->requiresConfirmation();

        $this->label('Ban');

        $this->modalHeading(fn (): string => 'Ban selected ' . $this->getPluralModelLabel());

        $this->modalSubmitActionLabel('Ban');

        $this->successNotificationTitle('Users are banned');

        $this->defaultColor('warning');

        $this->icon(FilamentIcon::resolve('actions::ban-action') ?? 'heroicon-o-no-symbol');

        $this->modalIcon(FilamentIcon::resolve('actions::ban-action.modal') ?? 'heroicon-o-no-symbol');

        $this->action(function (): void {
            if (!auth()->user()->can('ban-bulk-user')) {
                throw new AuthorizationException('You do not have permission to bulk ban users.');
            }

            $this->process(function (Collection $records) {
                $service = app(UserBanService::class);

                $service->banMany($records);
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();

        $this->hidden(function (HasTable $livewire): bool {
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
