<?php

namespace App\Filament\Filters\User;

use App\Models\Scopes\User\NotBannedScope;
use Exception;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class UserFilter extends SelectFilter
{
    /**
     * @throws Exception
     */
    public static function make(?string $name = 'user'): static
    {
        $filter = parent::make($name);

        return $filter
            ->label(__('User'))
            ->relationship(
                $name,
                'username',
                modifyQueryUsing: fn (Builder $query): Builder => $query
                    ->withoutGlobalScopes([NotBannedScope::class])
                    ->withTrashed(),
            )
            ->searchable();
    }
}
