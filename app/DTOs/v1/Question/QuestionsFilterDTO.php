<?php

namespace App\DTOs\v1\Question;

use App\DTOs\BaseDTO;

class QuestionsFilterDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $title,
        public readonly ?array $tagIds,
        public readonly ?array $gradeIds,
        public readonly ?array $companyIds,
    )
    {}
}
