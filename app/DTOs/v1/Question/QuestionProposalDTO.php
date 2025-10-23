<?php

namespace App\DTOs\v1\Question;

use App\DTOs\BaseDTO;

class QuestionProposalDTO extends BaseDTO
{
    public function __construct(
        public readonly string $title,
        public readonly bool $isAnonymous,
        public readonly ?string $answer,

        public readonly array $tagsExisting,
        public readonly array $tagsNew,

        public readonly array $grades,

        public readonly ?bool $metInRealInterview,
        public readonly ?string $whenAsked,

        public readonly ?int $companyExisting,
        public readonly ?string $companyNew,

        public readonly ?int $positionExisting,
        public readonly ?string $positionNew,
    ) {}
}
