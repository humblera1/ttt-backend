<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\v1\WithPlainErrorsValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Question\QuestionProposalRequest;
use App\Http\Requests\v1\Question\QuestionsListRequest;
use App\Http\Resources\v1\QuestionPreviewResource;
use App\Services\api\v1\QuestionProposalService;
use App\Services\api\v1\QuestionService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuestionController extends Controller
{
    public function __construct
    (
        protected QuestionService $service,
        protected QuestionProposalService $proposalService,
    ) {}

    public function list(QuestionsListRequest $request): AnonymousResourceCollection
    {
        return QuestionPreviewResource::collection(
            $this->service->getQuestionsList($request->getDTO())
        );
    }

    /**
     * @throws WithPlainErrorsValidationException
     */
    public function propose(QuestionProposalRequest $request)
    {
        if ($this->proposalService->propose($request->getDTO())) {
            return response()->created();
        }

        $this->responseWithPlainValidationError('Failed to save the proposal, please try again later.');
    }
}
