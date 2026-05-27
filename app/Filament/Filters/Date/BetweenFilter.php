<?php

namespace App\Filament\Filters\Date;

use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class BetweenFilter extends Filter
{
    /**
     * @throws Exception
     */
    public static function make(?string $name = 'created_at', ?string $column = null, ?string $label = null): static
    {
        $column ??= $name;

        $filter = parent::make($name);

        return $filter
            ->label($label ?? static::defaultLabel($name))
            ->form([
                DatePicker::make('from')
                    ->label(__('From')),
                DatePicker::make('until')
                    ->label(__('Until')),
            ])
            ->query(function (Builder $query, array $data) use ($column): Builder {
                return $query
                    ->when(
                        $data['from'] ?? null,
                        fn (Builder $q, $date): Builder => $q->whereDate($column, '>=', $date),
                    )
                    ->when(
                        $data['until'] ?? null,
                        fn (Builder $q, $date): Builder => $q->whereDate($column, '<=', $date),
                    );
            });
    }

    protected static function defaultLabel(string $name): string
    {
        return match ($name) {
            'created_at' => __('Created'),
            'updated_at' => __('Updated'),
            default => __('Date'),
        };
    }
}
