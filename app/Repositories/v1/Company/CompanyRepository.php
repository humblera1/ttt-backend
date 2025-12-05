<?php

namespace App\Repositories\v1\Company;

use App\Models\Company;
use App\Repositories\Repository;
use App\Traits\Repository\HasStatus;

class CompanyRepository extends Repository
{
    use HasStatus;

    public function __construct()
    {
        parent::__construct(Company::class);
    }
}
