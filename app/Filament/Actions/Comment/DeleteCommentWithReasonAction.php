<?php

namespace App\Filament\Actions\Comment;

use App\DTOs\v1\Comment\CommentDeleteWithReasonDTO;
use App\Enums\Comment\ReasonForDeletion;
use App\Models\Comment;
use App\Models\User;
use App\Services\api\v1\Comment\CommentService;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

class DeleteCommentWithReasonAction extends DeleteAction
{
    public static function getDefaultName(): ?string
    {
        return 'deleteWithReason';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Delete'));

        $this->modalHeading(__('Delete comment'));

        $this->modalSubmitActionLabel(__('Delete'));

        $this->form($this->getFormForModal());

        $this->action(function (Model $record, array $data): void {
            if (!$record instanceof Comment) {
                $this->failure();

                return;
            }

            /** @var User $user */
            $user = auth()->user();

            if (!$user->can('delete', $record)) {
                throw new AuthorizationException('You do not have permission to delete this comment.');
            }

            $reason = ReasonForDeletion::from($data['deleted_reason_code']);

            $result = $this->process(function () use ($record, $user, $reason, $data): bool {
                app(CommentService::class)->deleteWithReason(new CommentDeleteWithReasonDTO(
                    comment: $record,
                    moderator: $user,
                    reason: $reason,
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
            return !$record->trashed() && auth()->user()->can('delete', $record);
        });
    }

    /**
     * @return array<int, Select|Textarea>
     */
    private function getFormForModal(): array
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
                ->maxLength(10000),
        ];
    }
}
