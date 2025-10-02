<?php

namespace App\Services\api\v1;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyService
{
    public function getCompaniesList(?string $name = null, int $page = 1): LengthAwarePaginator
    {
        $query = Company::query()->approved();

        if ($name !== null) {
            $query->whereLike('name', '%' . $name . '%');
        }

        return $query->paginate($this->getPerPage($page));
    }

    protected function getPerPage(int $page): int
    {
        return $page === 1
            ? setting('company.first_page_per_page', 5)
            : setting('company.per_page', 15);
    }
}
