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
        $filterBy = $request->query('filter', 'all');

        if (!in_array($filterBy, self::FILTER_TARGET)) {
            return response()->json([
                'message' => 'Invalid filter parameter. Allowed values are: ' . implode(', ', self::FILTER_TARGET),
            ], 400);
        }

        $user = $request->user();

        $query = $user->notifications();

        if ($filterBy === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest()->paginate();

        $counts = [
            'all_notifications' => $user->notifications()->count(),
            'unread_notifications' => $user->notifications()->whereNull('read_at')->count()
        ];

        return NotificationResource::collection($notifications)
            ->additional(['counts' => $counts]);
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
