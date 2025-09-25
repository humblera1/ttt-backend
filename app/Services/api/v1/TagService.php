<?php

namespace App\Services\api\v1;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class TagService
{
    protected const int TAGS_LIMIT = 25;

    public function getTagsList(?string $name = null): Collection
    {
        $query = Tag::query()->approved();

        if ($name !== null) {
            $query->whereLike('name', '%' . $name . '%');
        }

        return $query->limit(self::TAGS_LIMIT)->get();
    }
}
