<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Position\PositionsListRequest;
use App\Http\Resources\v1\PositionResource;
use App\Services\api\v1\PositionService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PositionController extends Controller
{
    public function __construct(
        protected PositionService $service,
    ) {}

    public function list(PositionsListRequest $request): AnonymousResourceCollection
    {
        return PositionResource::collection($this->service->getPositionsList($request->get('name')));
    }
}
