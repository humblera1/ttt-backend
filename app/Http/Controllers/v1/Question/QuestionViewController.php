<?php

namespace App\Http\Controllers\v1\Question;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Services\api\v1\Question\QuestionViewService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QuestionViewController extends Controller
{
    public function __construct(
        private readonly QuestionViewService $viewService,
    ) {}

    public function store(Question $question, Request $request): Response
    {
        if ($question->status !== Status::Approved->value) {
            abort(404);
        }

        $this->viewService->recordView($question, (string) $request->ip());

        return response()->noContent();
    }
}
