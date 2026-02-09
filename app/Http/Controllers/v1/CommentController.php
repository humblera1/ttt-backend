<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Comment\CommentStoreRequest;
use App\Http\Resources\v1\CommentResource;
use App\Models\Comment;
use App\Models\Question;
use App\Services\api\v1\Comment\CommentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly CommentService $service,
    )
    {}

    /**
     * Display a listing of the comments for the given question.
     */
    public function list(Question $question): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Comment::class);

        return CommentResource::collection($this->service->getCommentsListForQuestion($question));
    }

    public function store(Question $question, CommentStoreRequest $request): CommentResource
    {
        $comment = $this->service->createForQuestion($question, $request->getDTO());

        return new CommentResource($comment->load('user'));
    }
}
