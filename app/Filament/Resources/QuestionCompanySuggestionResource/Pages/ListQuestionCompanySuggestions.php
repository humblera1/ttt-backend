<?php

namespace App\Filament\Resources\QuestionCompanySuggestionResource\Pages;

use App\Filament\Resources\QuestionCompanySuggestionResource;
use Filament\Resources\Pages\ListRecords;

class ListQuestionCompanySuggestions extends ListRecords
{
    protected static string $resource = QuestionCompanySuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
