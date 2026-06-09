<?php

namespace App\Filament\Columns\Statistic;

use Filament\Tables\Columns\TextColumn;

class MetInRealInterviewCountColumn extends TextColumn
{
    public static function make(string $name = 'met_in_real_interview_count'): static
    {
        return parent::make($name)
            ->label(__('Met count'))
            ->sortable()
            ->numeric();
    }
}
