<?php

namespace App\Filament\Columns\User;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserColumn extends TextColumn
{
    public static function make(string $name = 'user.username', ?string $relationship = null): static
    {
        $relationship ??= static::relationshipFromColumnName($name);

        return parent::make($name)
            ->label(__('User'))
            ->icon('heroicon-m-user')
            ->iconColor('primary')
            ->description(fn (Model $record): ?string => $record->{$relationship}?->full_name)
            ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas(
                $relationship,
                fn (Builder $userQuery): Builder => $userQuery->matchingSearchTerm($search),
            ));
    }

    protected static function relationshipFromColumnName(string $name): string
    {
        return str_contains($name, '.')
            ? explode('.', $name, 2)[0]
            : 'user';
    }
}
