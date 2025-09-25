<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Grade\GradesListRequest;
use App\Http\Resources\v1\GradeResource;
use App\Services\api\v1\GradeService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GradeController extends Controller
{
    public function __construct(
        protected GradeService $service,
    ) {}

    public function list(GradesListRequest $request): AnonymousResourceCollection
    {
        return GradeResource::collection($this->service->getGradesList($request->get('name')));
    }
}
