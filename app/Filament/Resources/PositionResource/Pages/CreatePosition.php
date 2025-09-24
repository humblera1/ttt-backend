<?php

namespace App\Filament\Resources\PositionResource\Pages;

use App\Enums\Status;
use App\Filament\Forms\Status\StatusSelect;
use App\Filament\Resources\PositionResource;
use App\Models\Position;
use Filament\Actions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreatePosition extends CreateRecord
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
                    StatusSelect::make(Position::class),
                ]),
        ]);
    }
}
