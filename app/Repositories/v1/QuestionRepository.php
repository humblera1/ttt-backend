<?php

namespace App\Repositories\v1;

use App\Models\Question;
use App\Repositories\Repository;

class QuestionRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(Question::class);
    }
}
