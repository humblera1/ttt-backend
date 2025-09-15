<?php

namespace App\Services\api\v1;

use App\Models\Position;
use Illuminate\Database\Eloquent\Collection;

class PositionService
{
    protected const int POSITIONS_LIMIT = 25;

    public function getPositionsList(?string $name = null): Collection
    {
        $query = Position::query()->approved();

        if ($name !== null) {
            $query->whereLike('name', '%' . $name . '%');
        }

        return $query->limit(self::POSITIONS_LIMIT)->get();
    }
}
