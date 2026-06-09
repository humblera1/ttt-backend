<?php

namespace App\Filament\Columns\Vote;

use Filament\Tables\Columns\TextColumn;

class LikesCountColumn extends TextColumn
{
    public static function make(string $name = 'likes_count'): static
    {
        return parent::make($name)
            ->label(__('Likes count'))
            ->sortable()
            ->badge()
            ->color(fn (int $state): string => self::colorForValue($state));
    }

    public static function colorForValue(int $state): string
    {
        if ($state > 0) {
            return 'success';
        }

        if ($state < 0) {
            return 'danger';
        }

        return 'gray';
    }
}
