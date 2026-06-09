<?php

namespace App\Filament\Columns\Statistic;

use Filament\Tables\Columns\TextColumn;

class ViewsCountColumn extends TextColumn
{
    public static function make(string $name = 'views_count'): static
    {
        return parent::make($name)
            ->label(__('Views count'))
            ->sortable()
            ->badge()
            ->color('info');
    }
}
