<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Forms\Status\StatusSelect;
use App\Filament\Resources\QuestionResource;
use App\Models\Question;
use App\Traits\Filament\Forms\Question\WithRelatedSelects;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateQuestion extends CreateRecord
{
    use WithRelatedSelects;

    protected static string $resource = QuestionResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    StatusSelect::make(Question::class),
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
        ])->columns();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['published_at'] = now();

        return $data;
    }
}
