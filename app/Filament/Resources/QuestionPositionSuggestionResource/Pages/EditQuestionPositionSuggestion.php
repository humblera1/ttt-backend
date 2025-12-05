<?php

namespace App\Filament\Resources\QuestionPositionSuggestionResource\Pages;

use App\Filament\Resources\QuestionPositionSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestionPositionSuggestion extends EditRecord
{
    protected static string $resource = QuestionPositionSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
