<?php

namespace App\Filament\Resources\QuestionCompanySuggestionResource\Pages;

use App\Filament\Resources\QuestionCompanySuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestionCompanySuggestion extends EditRecord
{
    protected static string $resource = QuestionCompanySuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
