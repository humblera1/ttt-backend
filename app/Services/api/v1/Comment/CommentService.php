<?php

namespace App\Services\api\v1\Comment;

use App\DTOs\v1\Comment\CommentDeleteWithReasonDTO;
use App\DTOs\v1\Comment\CommentStoreDTO;
use App\Enums\Comment\ReasonForDeletion;
use App\Events\v1\Comment\CommentCreated;
use App\Exceptions\v1\BusinessLogicException;
use App\Exceptions\v1\RepositoryException;
use App\Models\Comment;
use App\Models\Question;
use App\Models\User;
use App\Repositories\v1\Comment\CommentRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentService
{
    public function __construct(
        protected CommentRepository $repository,
    )
    {}

    /**
     * Returns a paginated list of comments associated with the specified question.
     */
    public function getCommentsListForQuestion(Question $question): LengthAwarePaginator
    {
        $perPage = setting('comments.per_page', 30);

        return $question->comments()
            ->with('user')
            ->orderBy('created_at')
            ->paginate($perPage);
    }

    /**
     * Creates and persists a new comment for the given question based on the provided DTO.
     */
    public function createForQuestion(Question $question, CommentStoreDTO $dto): Comment
    {
        return DB::transaction(function () use ($question, $dto) {
            $comment = new Comment([
                'body' => $dto->body,
                'parent_id' => $dto->parentId,
            ]);

            $comment->user()->associate($dto->user);
            $comment->question()->associate($question);

            $this->repository->save($comment);

            $comment->load('question', 'user');

            event(new CommentCreated($comment));

            return $comment;
        });
    }

    /**
     * Updates the body of the given comment and persists the changes.
     */
    public function updateBody(Comment $comment, string $body): Comment
    {
        try {
            $comment->body = $body;

            $this->repository->save($comment);
        } catch (RepositoryException $e) {
            Log::error('Failed to update comment', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }

        return $comment;
    }

    /**
     * Soft deletes the given comment on behalf of the specified user and records the deletion reason.
     */
    public function deleteByUser(Comment $comment, User $user): void
    {
        try {
            $comment->deleted_reason_code = ReasonForDeletion::UserRemoved;
            $comment->deleted_reason_comment = null;
            $comment->deleted_at = now();

            $comment->deletedBy()->associate($user);

            $this->repository->save($comment);
        } catch (RepositoryException $e) {
            Log::error('Failed to delete comment', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }

    /**
     * Soft deletes the given comment on behalf of a moderator and records the deletion reason.
     *
     * @throws BusinessLogicException
     */
    public function deleteWithReason(CommentDeleteWithReasonDTO $dto): void
    {
        try {
            $comment = $dto->comment;

            $comment->deleted_reason_code = $dto->reason;
            $comment->deleted_reason_comment = $dto->reasonComment;
            $comment->deleted_at = now();

            $comment->deletedBy()->associate($dto->moderator);

            $this->repository->save($comment);
        } catch (RepositoryException $e) {
            Log::error('Failed to delete comment with reason', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }

    /**
     * Restores a previously soft-deleted comment and clears its deletion metadata.
     */
    public function restore(Comment $comment): Comment
    {
        try {
            $comment->deleted_at = null;
            $comment->deleted_reason_code = null;
            $comment->deleted_reason_comment = null;
            $comment->deletedBy()->dissociate();

            $this->repository->save($comment);
        } catch (RepositoryException $e) {
            Log::error('Failed to restore comment', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }

        return $comment;
    }

    /**
     * Permanently deletes a soft-deleted comment.
     *
     * @throws BusinessLogicException
     */
    public function forceDelete(Comment $comment): void
    {
        try {
            $this->repository->bulkForceDelete([$comment->id]);
        } catch (RepositoryException $e) {
            Log::error('Failed to force delete comment', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }
}
