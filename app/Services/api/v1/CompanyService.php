<?php

namespace App\Services\api\v1;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

class CompanyService
{
    protected const int COMPANIES_LIMIT = 25;

    public function getCompaniesList(?string $name = null): Collection
    {
        $query = Company::query()->approved();

        if ($name !== null) {
            $query->whereLike('name', '%' . $name . '%');
        }

        return $query->limit(self::COMPANIES_LIMIT)->get();
    }
}
