<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Forms\Status\StatusSelect;
use App\Filament\Resources\TagResource;
use App\Models\Tag;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make()
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    StatusSelect::make(Tag::class),
                ]),
        ]);
    }
}
