<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()->paginate();

        return NotificationResource::collection($notifications);
    }

    public function lastNotification(Request $request)
    {
        $limit = $request->query->getInt('limit', 6);
        $user = $request->user();

        $notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return NotificationResource::collection($notifications);
    }

    public function show(Request $request, string $id)
    {
        $user = $request->user();

        $notification = $user->notifications()->findOrFail($id);

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return new NotificationResource($notification);
    }
}
