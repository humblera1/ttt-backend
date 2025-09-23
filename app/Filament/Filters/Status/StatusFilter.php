<?php

namespace App\Filament\Filters\Status;

use App\Enums\Status;
use Exception;
use Filament\Tables\Filters\SelectFilter;

class StatusFilter extends SelectFilter
{
    /**
     * @throws Exception
     */
    public static function make(?string $name = 'status'): static
    {
        $filter = parent::make($name);

        return $filter
            ->attribute('status')
            ->options(Status::options());
    }
}
