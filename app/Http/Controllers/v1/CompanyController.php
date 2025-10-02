<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Company\CompaniesListRequest;
use App\Http\Resources\v1\PositionResource;
use App\Services\api\v1\CompanyService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyService $service,
    ) {}

    public function list(CompaniesListRequest $request): AnonymousResourceCollection
    {
        return PositionResource::collection($this->service->getCompaniesList(
            $request->get('name'),
            $request->get('page', 1)
        ));
    }
}
