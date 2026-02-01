<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\v1\BusinessLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Notification\NotificationsListRequest;
use App\Http\Resources\v1\Notification\NotificationResource;
use App\Models\UserNotification;
use App\Services\api\v1\Notification\NotificationService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

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
                $request->user(),
                $request->input('per_page'),
            )
        );
    }

    /**
     * @throws BusinessLogicException
     */
    public function markAsRead(UserNotification $notification): Response
    {
        $this->service->markAsRead($notification);

        return response()->noContent();
    }
}
