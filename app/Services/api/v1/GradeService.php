<?php

namespace App\Services\api\v1;

use App\Models\Grade;
use Illuminate\Database\Eloquent\Collection;

class GradeService
{
    public function getGradesList(?string $name = null): Collection
    {
        $query = Grade::query();

        if ($name !== null) {
            $query->whereLike('name', '%' . $name . '%');
        }

        return $query->get();
    }
}
