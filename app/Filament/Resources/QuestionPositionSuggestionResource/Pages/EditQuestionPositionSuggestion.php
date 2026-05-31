<?php

namespace App\Filament\Resources\QuestionPositionSuggestionResource\Pages;

use App\Filament\Actions\Form\Status\Suggestion\PositionApproveAction;
use App\Filament\Actions\Form\Status\Suggestion\PositionReturnToReviewAction;
use App\Filament\Actions\Form\Status\Shared\ResetAction;
use App\Filament\Actions\Form\Status\Suggestion\RejectAction;
use App\Filament\Resources\QuestionPositionSuggestionResource;
use App\Filament\Resources\Widgets\Status\StatusWithReviewBadge;
use App\Models\QuestionPositionSuggestion;
use App\Traits\Filament\Forms\Utils\HasLinkUtils;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditQuestionPositionSuggestion extends EditRecord
{
    use HasLinkUtils;

    protected static string $resource = QuestionPositionSuggestionResource::class;

    public function getHeading(): string
    {
        return 'Question & Position Relation';
    }

    public function getTitle(): string
    {
        return 'Question & Position Relation';
    }

    public function getBreadcrumb(): string
    {
        return 'Moderate';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatusWithReviewBadge::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ApproveAction::make(),
            ResetAction::make(),
            ReturnToReviewAction::make(),
            RejectAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    Placeholder::make('question_link')
                        ->label('Question:')
                        ->content(function (QuestionPositionSuggestion $record): HtmlString {
                            return $this->createLink(
                                "/admin/questions/$record->question_id/edit",
                                $record->question->title,
                            );
                        }),
                    Placeholder::make('position_link')
                        ->label('Company:')
                        ->content(function (QuestionPositionSuggestion $record): HtmlString {
                            return $this->createLink(
                                "/admin/positions/$record->position_id/edit",
                                $record->position->name,
                            );
                        }),
                    Placeholder::make('evidence_count')
                        ->label('Evidence Count:')
                        ->content(fn (QuestionPositionSuggestion $record): string => $record->evidence_count),
                ])
                ->compact()
                ->columnSpan(8),

            Section::make()
                ->schema([
                    Placeholder::make('position_question_exists')
                        ->label('Relation Exists:')
                        ->content(function (QuestionPositionSuggestion $record): string {
                            $exists = $record->question
                                ->positions()
                                ->where('positions.id', $record->position_id)
                                ->exists();

                            return $exists ? 'Yes' : 'No';
                        }),
                ])
                ->compact()
                ->columnSpan(4),
        ])->columns(12);
    }
}
