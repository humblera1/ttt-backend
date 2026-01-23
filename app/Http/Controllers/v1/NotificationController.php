<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Notification\NotificationsListRequest;
use App\Http\Resources\v1\Notification\NotificationResource;
use App\Services\api\v1\Notification\NotificationService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $service,
    )
    {}

    public function list(NotificationsListRequest $request): AnonymousResourceCollection
    {
        return NotificationResource::collection(
            $this->service->getNotificationsFor(
                Auth::user(),
                $request->input('per_page'),
            )
        );
    }
}
