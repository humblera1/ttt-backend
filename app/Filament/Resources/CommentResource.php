<?php

namespace App\Filament\Resources;

use App\Filament\Columns\Trash\TrashedColumn;
use App\Filament\Columns\User\UserColumn;
use App\Filament\Filters\Date\BetweenFilter;
use App\Filament\Filters\Trash\TrashedFilter;
use App\Filament\Filters\User\UserFilter;
use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationGroup = 'Main';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    public static function getNavigationLabel(): string
    {
        return __('Comments');
    }

    public static function getModelLabel(): string
    {
        return __('Comment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Comments');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed()
            ->with(['question', 'user']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question.title')
                    ->label(__('Question'))
                    ->searchable()
                    ->url(fn (Comment $record): string => QuestionResource::getUrl(
                        'edit',
                        ['record' => $record->question_id]
                    ))
                    ->limit(50),
                UserColumn::make(),
                TextColumn::make('body')
                    ->label(__('Text'))
                    ->limit()
                    ->searchable(),
                TrashedColumn::make(),
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->sortable()
                    ->since()
                    ->dateTooltip(),
                TextColumn::make('updated_at')
                    ->label(__('Updated'))
                    ->sortable()
                    ->since()
                    ->dateTooltip()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('question_id')
                    ->label(__('Question'))
                    ->relationship('question', 'title')
                    ->searchable()
                    ->preload(),
                UserFilter::make(),
                TrashedFilter::make(),
                BetweenFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
