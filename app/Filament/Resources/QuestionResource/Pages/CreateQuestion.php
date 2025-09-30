<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Enums\Status;
use App\Filament\Resources\QuestionResource;
use App\Models\Company;
use App\Models\Grade;
use App\Models\Tag;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

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
                    Select::make('grades')
                        ->multiple()
                        ->preload()
                        ->relationship(
                            name: 'grades',
                            titleAttribute: 'name',
                        )
                        ->visible(auth()->user()->can('create', Grade::class)),
                    Select::make('tags')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->optionsLimit(10)
                        ->relationship(
                            name: 'tags',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query->approved(),
                        )
                        ->createOptionForm([
                            TextInput::make('name')->required(),
                        ])
                        ->createOptionUsing(function (array $data) {
                            $data['status'] = Status::Approved->value;

                            return Tag::create($data);
                        })
                        ->visible(auth()->user()->can('create', Tag::class)),
                    Select::make('companies')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->optionsLimit(10)
                        ->relationship(
                            name: 'companies',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query->approved(),
                        )
                        ->createOptionForm([
                            TextInput::make('name')->required(),
                        ])
                        ->createOptionUsing(function (array $data) {
                            $data['status'] = Status::Approved->value;

                            return Company::create($data);
                        })
                        ->visible(auth()->user()->can('create', Company::class)),
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
