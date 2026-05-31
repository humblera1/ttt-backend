<?php

namespace App\Filament\Resources\QuestionResource\RelationManagers;

use App\Filament\Actions\Table\Moderation\Comment\ToggleCommentTrashedAction;
use App\Filament\Columns\Trash\TrashedColumn;
use App\Filament\Columns\User\UserColumn;
use App\Filament\Resources\CommentResource;
use App\Models\Comment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $title = 'Comments';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('viewAny', Comment::class);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withTrashed()->with('user'))
            ->defaultSort('created_at', 'desc')
            ->columns([
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
            ])
            ->headerActions([])
            ->actions([
                ToggleCommentTrashedAction::make(),
                EditAction::make()
                    ->url(fn (Comment $record): string => CommentResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
