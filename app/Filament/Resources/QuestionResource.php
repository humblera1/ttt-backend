<?php

namespace App\Filament\Resources;

use App\Enums\Grade;
use App\Filament\Actions\Table\Delete\Shared\DeleteBulkAction;
use App\Filament\Actions\Table\Delete\Shared\ForceDeleteBulkAction;
use App\Filament\Actions\Table\Status\Shared\{ApproveAction, RejectAction, ResetQuestionAction};
use App\Filament\Columns\Status\StatusColumn;
use App\Filament\Columns\Vote\LikesCountColumn;
use App\Filament\Filters\Status\StatusFilter;
use App\Filament\Filters\Trash\TrashedFilter;
use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\QuestionResource\RelationManagers\StatisticsRelationManager;
use App\Models\Question;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\{DeleteAction, EditAction, RestoreAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationGroup = 'Main';

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed();
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
                TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('is_premium')
                    ->sortable()
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'premium' : 'common'),
                StatusColumn::make(),
                TextColumn::make('rating')
                    ->sortable()
                    ->icon('heroicon-m-star')
                    ->iconColor('primary'),
                LikesCountColumn::make(),
                TextColumn::make('created_at')
                    ->sortable()
                    ->since(),
                TextColumn::make('grades.name')
                    ->label('Grades')
                    ->badge()
                    ->color(fn (string $state) => Grade::colorByValue($state)),
            ])
            ->filters([
                StatusFilter::make(),
                TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('is_premium')
                    ->placeholder(__('All'))
                    ->trueLabel(__('Premium'))
                    ->falseLabel(__('Common')),
                Tables\Filters\SelectFilter::make('grades')
                    ->relationship('grades', 'name')
                    ->preload(),
                Tables\Filters\SelectFilter::make('tags')
                    ->relationship(
                        'tags',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->approved()
                    )
                    ->multiple()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('companies')
                    ->relationship(
                        'companies',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->approved()
                    )
                    ->searchable(),
                Tables\Filters\SelectFilter::make('positions')
                    ->relationship(
                        'positions',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->approved()
                    )
                    ->searchable(),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship(
                        'user',
                        'username',
                    )
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    ApproveAction::make(),
                    RejectAction::make(),
                    ResetQuestionAction::make(),
                ]),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->model(Question::class),
                    ForceDeleteBulkAction::make()
                        ->model(Question::class),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
            StatisticsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
