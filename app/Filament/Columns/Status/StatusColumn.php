<?php

namespace App\Filament\Columns\Status;

use Filament\Tables\Columns\TextColumn;
use App\Enums\Status;

class StatusColumn extends TextColumn
{
    public static function make(string $name = 'status'): static
    {
        return parent::make($name)
            ->sortable()
            ->badge()
            ->color(fn (string $state): string => Status::colorByValue($state));
    }
}
