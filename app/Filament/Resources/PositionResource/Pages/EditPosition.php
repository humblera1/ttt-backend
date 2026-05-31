<?php

namespace App\Filament\Resources\PositionResource\Pages;

use App\Filament\Actions\Page\Delete\Shared\ForceDeleteAction;
use App\Filament\Forms\Status\StatusSelect;
use App\Filament\Resources\PositionResource;
use Filament\Actions\{DeleteAction, RestoreAction};
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditPosition extends EditRecord
{
    protected static string $resource = PositionResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make()
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    StatusSelect::make($this->record),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
