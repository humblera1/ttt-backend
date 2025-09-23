<?php

namespace App\Filament\Filters\Trash;

use Exception;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

class TrashedFilter extends TernaryFilter
{
    /**
     * @throws Exception
     */
    public static function make(?string $name = 'deleted_at'): static
    {
        $filter = parent::make($name);

        return $filter
            ->label(__('Trash'))
            ->placeholder(__('Active'))
            ->trueLabel(__('Trashed'))
            ->falseLabel(__('All'))
            ->queries(
                true: fn (Builder $query): Builder => $query->whereNotNull($name),
                false: fn (Builder $query): Builder => $query,
                blank: fn (Builder $query): Builder => $query->whereNull($name),
            );
    }
}
