<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private const FILTER_TARGET = ['all', 'unread'];

    public function index(Request $request)
    {
        $user = $request->user();
        $filterBy = $request->query->get('filter', 'all');

        $builder = $filterBy === 'unread'
            ? $user->unreadNotifications()
            : $user->notifications();

        $notifications = $builder->paginate();

        $countAllNotification = $user->notifications()->count();
        $countAllUnread = $user->unreadNotifications()->count();

        return NotificationResource::collection($notifications)
            ->additional([
                'counts' => [
                    'all_notifications' => $countAllNotification,
                    'unread_notifications' => $countAllUnread,
                ],
            ]);
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

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()->get();

        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'status' => 200,
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->user();

        $notification = $user->notifications()->findOrFail($id);

        $deleted = $notification->delete();

        return response()->json([
            'status' => $deleted,
        ]);
    }
}
