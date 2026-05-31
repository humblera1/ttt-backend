<?php

namespace App\Filament\Resources\QuestionCompanySuggestionResource\Pages;

use App\Filament\Actions\Form\Status\Suggestion\CompanyApproveAction;
use App\Filament\Actions\Form\Status\Suggestion\CompanyReturnToReviewAction;
use App\Filament\Actions\Form\Status\Shared\ResetAction;
use App\Filament\Actions\Form\Status\Suggestion\RejectAction;
use App\Filament\Resources\QuestionCompanySuggestionResource;
use App\Filament\Resources\Widgets\Status\StatusWithReviewBadge;
use App\Models\QuestionCompanySuggestion;
use App\Traits\Filament\Forms\Utils\HasLinkUtils;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditQuestionCompanySuggestion extends EditRecord
{
    use HasLinkUtils;

    protected static string $resource = QuestionCompanySuggestionResource::class;

    public function getHeading(): string
    {
        return 'Question & Company Relation';
    }

    public function getTitle(): string
    {
        return 'Question & Company Relation';
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
                        ->content(function (QuestionCompanySuggestion $record): HtmlString {
                            return $this->createLink(
                                "/admin/questions/$record->question_id/edit",
                                $record->question->title,
                            );
                        }),
                    Placeholder::make('company_link')
                        ->label('Company:')
                        ->content(function (QuestionCompanySuggestion $record): HtmlString {
                            return $this->createLink(
                                "/admin/companies/$record->company_id/edit",
                                $record->company->name,
                            );
                        }),
                    Placeholder::make('evidence_count')
                        ->label('Evidence Count:')
                        ->content(fn (QuestionCompanySuggestion $record): string => $record->evidence_count),
                ])
                ->compact()
                ->columnSpan(8),

            Section::make()
                ->schema([
                    Placeholder::make('company_question_exists')
                        ->label('Relation Exists:')
                        ->content(function (QuestionCompanySuggestion $record): string {
                            $exists = $record->question
                                ->companies()
                                ->where('companies.id', $record->company_id)
                                ->exists();

                            return $exists ? 'Yes' : 'No';
                        }),
                ])
                ->compact()
                ->columnSpan(4),
        ])->columns(12);
    }
}
