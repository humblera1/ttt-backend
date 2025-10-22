<?php

namespace App\Interfaces\v1\Resolving;

use App\Models\Company;

class CompanyResolver extends Resolver
{
    public string $modelClass = Company::class;
}
