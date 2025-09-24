<?php

namespace App\Filament\Forms\Status;

use App\Enums\Status;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;

class StatusSelect
{
    public static function make(string|Model $record): Select
    {
        return Select::make('status')
            ->required()
            ->options(Status::options())
            ->default(Status::Approved->value)
            ->selectablePlaceholder(false)
            ->visible(auth()->user()->can('changeStatus', $record));
    }
}
