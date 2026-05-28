<?php

namespace App\Filament\Actions\Comment;

use App\Filament\Resources\CommentResource;
use App\Models\Comment;
use App\Services\api\v1\Comment\CommentService;
use Filament\Actions\RestoreAction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

class RestoreCommentAction extends RestoreAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function (): void {
            $result = $this->process(function (Model $record): bool {
                if (!$record instanceof Comment) {
                    return false;
                }

                if (!auth()->user()->can('restore', $record)) {
                    throw new AuthorizationException('You do not have permission to restore this comment.');
                }

                app(CommentService::class)->restore($record);

                return true;
            });

            if (!$result) {
                $this->failure();

                return;
            }

            $this->success();
        });

        $this->visible(function (Model $record): bool {
            return $record->trashed() && auth()->user()->can('restore', $record);
        });
    }
}
