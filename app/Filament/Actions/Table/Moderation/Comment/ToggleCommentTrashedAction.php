<?php

namespace App\Filament\Actions\Table\Moderation\Comment;

use App\DTOs\v1\Comment\CommentDeleteWithReasonDTO;
use App\Enums\Comment\ReasonForDeletion;
use App\Models\Comment;
use App\Models\User;
use App\Services\api\v1\Comment\CommentService;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\Action;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

class ToggleCommentTrashedAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'toggleCommentTrashed';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (Comment $record): string => $record->trashed() ? __('Restore') : __('Delete'));

        $this->icon(fn (Comment $record): string => $record->trashed() ? 'heroicon-m-arrow-path' : 'heroicon-m-trash');

        $this->color(fn (Comment $record): string => $record->trashed() ? 'gray' : 'danger');

        $this->requiresConfirmation();

        $this->modalHeading(fn (Comment $record): string => $record->trashed() ? __('Restore comment') : __('Delete comment'));

        $this->modalSubmitActionLabel(fn (Comment $record): string => $record->trashed() ? __('Restore') : __('Delete'));

        $this->form(fn (Comment $record): array => $record->trashed() ? [] : $this->getDeleteFormSchema());

        $this->action(function (Comment $record, array $data): void {
            /** @var User $user */
            $user = auth()->user();

            $result = $this->process(function () use ($record, $user, $data): bool {
                $service = app(CommentService::class);

                if ($record->trashed()) {
                    if (!$user->can('restore', $record)) {
                        throw new AuthorizationException('You do not have permission to restore this comment.');
                    }

                    $service->restore($record);

                    return true;
                }

                if (!$user->can('delete', $record)) {
                    throw new AuthorizationException('You do not have permission to delete this comment.');
                }

                $service->deleteWithReason(new CommentDeleteWithReasonDTO(
                    comment: $record,
                    moderator: $user,
                    reason: ReasonForDeletion::from($data['deleted_reason_code']),
                    reasonComment: $data['deleted_reason_comment'] ?? null,
                ));

                return true;
            });

            if (!$result) {
                $this->failure();

                return;
            }

            $this->success();
        });

        $this->visible(function (Model $record): bool {
            if (!$record instanceof Comment) {
                return false;
            }

            return $record->trashed()
                ? auth()->user()->can('restore', $record)
                : auth()->user()->can('delete', $record);
        });
    }

    /**
     * @return array<int, Select|Textarea>
     */
    protected function getDeleteFormSchema(): array
    {
        return [
            Select::make('deleted_reason_code')
                ->label(__('Reason'))
                ->required()
                ->options(ReasonForDeletion::options()),
            Textarea::make('deleted_reason_comment')
                ->label(__('Comment'))
                ->rows(3)
                ->placeholder(__('Optional comment'))
                ->maxLength(1000),
        ];
    }
}
