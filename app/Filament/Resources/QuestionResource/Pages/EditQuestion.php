<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Actions\Page\Delete\Shared\ForceDeleteAction;
use App\Filament\Actions\Page\Status\Question\{ApproveAction, RejectAction, RejectDuplicateAction, ResetAction};
use App\Filament\Resources\QuestionResource;
use App\Filament\Resources\Widgets\Status\StatusBadge;
use App\Models\Question;
use App\Traits\Filament\Forms\Question\WithRelatedSelects;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditQuestion extends EditRecord
{
    use WithRelatedSelects;

    protected static string $resource = QuestionResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatusBadge::class,
        ];
    }


    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    Toggle::make('is_premium'),
                    RichEditor::make('answer')
                        ->toolbarButtons([
                            'blockquote',
                            'bold',
                            'bulletList',
                            'codeBlock',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                            'subscript'
                        ])
                        ->maxWidth(200),
                ])
                ->compact()
                ->columnSpan(1),

            Section::make()
                ->schema([
                    $this->getGradesSelect(),
                    $this->getTagsSelect(),
                    $this->getCompaniesSelect(),
                    $this->getPositionsSelect(),
                ])
                ->compact()
                ->columnSpan(1),

            Section::make(__('Engagement'))
                ->schema([
                    Placeholder::make('likes_count')
                        ->label(__('Likes count'))
                        ->content(fn (?Question $record): string => (string) ($record?->likes_count ?? 0)),
                    Placeholder::make('met_in_real_interview_count')
                        ->label(__('Met in interview count'))
                        ->content(fn (?Question $record): string => (string) ($record?->met_in_real_interview_count ?? 0)),
                ])
                ->icon('heroicon-m-hand-thumb-up')
                ->collapsed()
                ->columnSpanFull(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ApproveAction::make(),
            RejectAction::make(),
            RejectDuplicateAction::make(),
            ResetAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
