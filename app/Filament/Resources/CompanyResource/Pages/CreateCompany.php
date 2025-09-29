<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Forms\Status\StatusSelect;
use App\Filament\Resources\CompanyResource;
use App\Models\Company;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateCompany extends CreateRecord
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
                    StatusSelect::make(Company::class),
                ]),
        ]);
    }
}
