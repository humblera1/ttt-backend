<?php

namespace App\Interfaces\v1\Resolving;

use App\Models\Position;

class PositionResolver extends Resolver
{
    public string $modelClass = Position::class;
}
