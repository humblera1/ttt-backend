<?php

namespace App\Services\api\v1\Comment;

use App\DTOs\v1\Comment\CommentStoreDTO;
use App\Events\v1\Comment\CommentCreated;
use App\Exceptions\v1\BusinessLogicException;
use App\Exceptions\v1\RepositoryException;
use App\Models\Comment;
use App\Models\Question;
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
     * Return a list of the comments for the given question.
     */
    public function getCommentsListForQuestion(Question $question): LengthAwarePaginator
    {
        $perPage = setting('comments.per_page', 30);

        return $question->comments()
            ->with('user')
            ->orderBy('created_at')
            ->paginate($perPage);
    }

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

            event(new CommentCreated($comment));

            return $comment;
        });
    }

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
}
