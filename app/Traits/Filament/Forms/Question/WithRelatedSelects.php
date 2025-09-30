<?php

namespace App\Traits\Filament\Forms\Question;

use App\Enums\Status;
use App\Models\Company;
use App\Models\Grade;
use App\Models\Tag;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait WithRelatedSelects
{
    protected function getGradesSelect(): Select
    {
        return Select::make('grades')
            ->multiple()
            ->preload()
            ->relationship(
                name: 'grades',
                titleAttribute: 'name',
            )
            ->visible(auth()->user()->can('viewAny', Grade::class));
    }

    protected function getTagsSelect(): Select
    {
        return Select::make('tags')
            ->multiple()
            ->searchable()
            ->preload()
            ->optionsLimit(10)
            ->relationship(
                name: 'tags',
                titleAttribute: 'name',
                modifyQueryUsing: fn (Builder $query) => $query->approved(),
            )
            ->when(
                auth()->user()->can('create', Tag::class),
                fn ($field) => $field
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                    ])
                    ->createOptionUsing(function (array $data) {
                        $data['status'] = Status::Approved->value;

                        return Tag::create($data);
                    })
            )
            ->visible(auth()->user()->can('viewAny', Tag::class));
    }

    protected function getCompaniesSelect(): Select
    {
        return Select::make('companies')
            ->multiple()
            ->searchable()
            ->preload()
            ->optionsLimit(10)
            ->relationship(
                name: 'companies',
                titleAttribute: 'name',
                modifyQueryUsing: fn (Builder $query) => $query->approved(),
            )
            ->when(
                auth()->user()->can('create', Company::class),
                fn ($field) => $field
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                    ])
                    ->createOptionUsing(function (array $data) {
                        $data['status'] = Status::Approved->value;

                        return Company::create($data);
                    })
            )
            ->visible(auth()->user()->can('viewAny', Company::class));
    }
}
