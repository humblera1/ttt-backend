<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Tag\TagsListRequest;
use App\Http\Resources\v1\TagResource;
use App\Services\api\v1\TagService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    public function __construct(
        protected TagService $service
    ) {}

    public function list(TagsListRequest $request): AnonymousResourceCollection
    {
        return TagResource::collection($this->service->getTagsList(
            $request->get('name'),
            $request->get('page', 1),
        ));
    }
}
