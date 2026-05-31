<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Actions\Page\Delete\Shared\ForceDeleteAction;
use App\Filament\Forms\Status\StatusSelect;
use App\Filament\Resources\CompanyResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

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
