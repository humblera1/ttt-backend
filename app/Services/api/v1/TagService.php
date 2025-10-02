<?php

namespace App\Services\api\v1;

use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TagService
{
    public function getTagsList(?string $name = null, int $page = 1): LengthAwarePaginator
    {
        $query = Tag::query()->approved();

        if ($name !== null) {
            $query->whereLike('name', '%' . $name . '%');
        }

        return $query->paginate($this->getPerPage($page));
    }

    protected function getPerPage(int $page): int
    {
        return $page === 1
            ? setting('tag.first_page_per_page', 5)
            : setting('tag.per_page', 15);
    }
}
