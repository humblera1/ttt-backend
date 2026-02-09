<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Comment\CommentStoreRequest;
use App\Http\Requests\v1\Comment\CommentUpdateRequest;
use App\Http\Resources\v1\CommentResource;
use App\Models\Comment;
use App\Models\Question;
use App\Services\api\v1\Comment\CommentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly CommentService $service,
    )
    {}

    /**
     * Returns a paginated list of comments for the given question.
     */
    public function list(Question $question): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Comment::class);

        return CommentResource::collection($this->service->getCommentsListForQuestion($question));
    }

    /**
     * Creates a new comment for the given question using the provided request data.
     */
    public function store(Question $question, CommentStoreRequest $request): CommentResource
    {
        $comment = $this->service->createForQuestion($question, $request->getDTO());

        return new CommentResource($comment->load('user'));
    }

    /**
     * Updates the body of the specified comment with validated request data.
     */
    public function update(Comment $comment, CommentUpdateRequest $request): CommentResource
    {
        $this->authorize('update', $comment);

        $comment = $this->service->updateBody($comment, $request->validated('body'));

        return new CommentResource($comment->load('user'));
    }

    /**
     * Soft deletes the specified comment on behalf of the authenticated user.
     */
    public function delete(Comment $comment, Request $request): Response
    {
        $this->authorize('delete', $comment);

        $this->service->deleteByUser($comment, $request->user());

        return response()->noContent();
    }

    /**
     * Restores a previously soft-deleted comment and returns its resource representation.
     */
    public function restore(Comment $comment): CommentResource
    {
        $this->authorize('restore', $comment);

        $comment = $this->service->restore($comment);

        return new CommentResource($comment->load('user'));
    }
}
