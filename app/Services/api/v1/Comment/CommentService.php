<?php

namespace App\Services\api\v1\Comment;

use App\Models\Question;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentService
{
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
}
