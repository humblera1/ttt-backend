<?php

namespace App\Filament\Actions\Question;

use App\Interfaces\v1\Status\StatusInterface;
use Filament\Actions\Action;

class RejectQuestionAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reset';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Reject'));

        $this->defaultColor('danger');

        $this->icon('heroicon-m-x-mark');

//        $this->groupedIcon(FilamentIcon::resolve('actions::delete-action.grouped') ?? 'heroicon-m-trash');

//        $this->modalIcon(FilamentIcon::resolve('actions::delete-action.modal') ?? 'heroicon-o-trash');

        $this->visible(function (StatusInterface $record) {
            return $record->isPending() && auth()->user()->can('changeStatus', $record);
        });
    }
}
