<?php

namespace App\DTOs\v1\Question;

use App\DTOs\BaseDTO;

class QuestionFeedbackDTO extends BaseDTO
{
    public function __construct(
        public readonly bool $metInRealInterview,
        public readonly ?string $whenAsked,

        public readonly ?int $companyExisting,
        public readonly ?string $companyNew,

        public readonly ?int $positionExisting,
        public readonly ?string $positionNew,
    )
    {}
}
