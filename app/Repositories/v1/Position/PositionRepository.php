<?php

namespace App\Repositories\v1\Position;

use App\Models\Position;
use App\Repositories\Repository;
use App\Traits\Repository\HasStatus;

class PositionRepository extends Repository
{
    use HasStatus;

    public function __construct()
    {
        parent::__construct(Position::class);
    }
}
