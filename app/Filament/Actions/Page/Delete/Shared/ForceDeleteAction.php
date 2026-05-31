<?php

namespace App\Filament\Actions\Page\Delete\Shared;

use Filament\Actions\ForceDeleteAction as BaseForceDeleteAction;
use Illuminate\Database\Eloquent\Model;

class ForceDeleteAction extends BaseForceDeleteAction
{
    public static function make(?string $name = null): static
    {
        $action = parent::make($name);

        $action->visible(fn (Model $record) => auth()->user()->can('forceDelete', $record));

        return $action;
    }
}
