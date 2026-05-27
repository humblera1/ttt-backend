<?php

namespace App\Filament\Columns\Trash;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrashedColumn extends TextColumn
{
    public static function make(string $name = 'deleted_at'): static
    {
        return parent::make($name)
            ->label(__('Status'))
            ->sortable()
            ->badge()
            ->getStateUsing(fn (Model $record): string => static::isTrashed($record) ? __('Trashed') : __('Active'))
            ->color(fn (Model $record): string => static::isTrashed($record) ? 'danger' : 'success');
    }

    protected static function isTrashed(Model $record): bool
    {
        if (in_array(SoftDeletes::class, class_uses_recursive($record))) {
            return $record->trashed();
        }

        return filled($record->getAttribute('deleted_at'));
    }
}
