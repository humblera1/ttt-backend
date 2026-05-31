<?php

namespace App\Filament\Resources\CommentResource\Pages;

use App\Filament\Actions\Page\Delete\Comment\DeleteCommentWithReasonAction;
use App\Filament\Actions\Page\Restore\Comment\RestoreCommentAction;
use App\Filament\Actions\Page\Delete\Shared\ForceDeleteAction;
use App\Filament\Resources\CommentResource;
use App\Filament\Resources\QuestionResource;
use App\Models\Comment;
use App\Services\api\v1\Comment\CommentService;
use App\Traits\Filament\Forms\Utils\HasLinkUtils;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class EditComment extends EditRecord
{
    use HasLinkUtils;

    protected static string $resource = CommentResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make(__('Context'))
                ->schema([
                    Placeholder::make('question.title')
                        ->label(__('Question'))
                        ->content(fn (?Comment $record): HtmlString => $this->getQuestionLinkPlaceholder($record)),
                    Placeholder::make('parent_id')
                        ->label(__('Parent'))
                        ->content(fn (?Comment $record): HtmlString => $this->getParentCommentLinkPlaceholder($record)),
                    Placeholder::make('user.username')
                        ->label(__('User'))
                        ->content(fn (?Comment $record): ?string => $record->user->username),
                    Placeholder::make('trashed')
                        ->label(__('Status'))
                        ->content(fn (?Comment $record): string => $this->getTrashedStatusLabel($record)),
                ])
                ->icon('heroicon-m-information-circle')
                ->collapsed(),

            Section::make(__('Body'))
                ->schema([
                    Textarea::make('body')
                        ->required()
                        ->minLength(1)
                        ->maxLength(1000)
                        ->rows(10)
                        ->disabled(static fn (?Comment $record): bool => (bool) $record?->trashed()),
                ])
                ->icon('heroicon-m-chat-bubble-left-right'),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteCommentWithReasonAction::make(),
            RestoreCommentAction::make(),
            ForceDeleteAction::make(),
        ];
    }

    protected function getQuestionLinkPlaceholder(?Comment $record): HtmlString
    {
        return $this->createLink(
            QuestionResource::getUrl('edit', ['record' => $record->question_id]),
            $record->question->title,
        );
    }

    protected function getParentCommentLinkPlaceholder(?Comment $record): HtmlString
    {
        if (!$record?->parent) {
            return $this->getEmptyPlaceholder();
        }

        $url = CommentResource::getUrl('edit', ['record' => $record->parent_id]);

        $label = sprintf(
            '#%d — %s',
            $record->parent_id,
            $record->parent->user?->username ?? '—',
        );

        return $this->createLink($url, $label);
    }

    protected function getTrashedStatusLabel(?Comment $record): string
    {
        return $record?->trashed() ? __('Trashed') : __('Active');
    }

    protected function getEmptyPlaceholder(): HtmlString
    {
        return new HtmlString('—');
    }

    protected function getRecordQuery(): Builder
    {
        return parent::getRecordQuery()
            ->with(['question', 'user', 'parent.user'])
            ->withTrashed();
    }
}
