<?php

namespace App\Filament\Resources\QuestionPositionSuggestionResource\Pages;

use App\Filament\Resources\QuestionPositionSuggestionResource;
use Filament\Resources\Pages\ListRecords;

class ListQuestionPositionSuggestions extends ListRecords
{
    protected static string $resource = QuestionPositionSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
