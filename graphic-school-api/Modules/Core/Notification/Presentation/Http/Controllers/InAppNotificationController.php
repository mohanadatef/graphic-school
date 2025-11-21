<?php

namespace Modules\Core\Notification\Presentation\Http\Controllers;

use App\Support\Controllers\BaseController;
use Modules\Core\Notification\Models\InAppNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * CHANGE-003: In-App Notifications System & Notification Center
 */
class InAppNotificationController extends BaseController
{
    /**
     * Get all notifications for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = InAppNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Filter by read/unread
        if ($request->has('read')) {
            if ($request->boolean('read')) {
                $query->read();
            } else {
                $query->unread();
            }
        }

        // Filter by type
        if ($request->has('type')) {
            $query->ofType($request->input('type'));
        }

        // Filter by category
        if ($request->has('category')) {
            $query->ofCategory($request->input('category'));
        }

        $notifications = $query->paginate($request->input('per_page', 15));

        return $this->paginated($notifications, 'Notifications retrieved successfully');
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $count = InAppNotification::where('user_id', $user->id)
            ->unread()
            ->count();

        return $this->success(['count' => $count], 'Unread count retrieved');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $notification = InAppNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $notification->markAsRead();

        return $this->success($notification, 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        
        InAppNotification::where('user_id', $user->id)
            ->unread()
            ->update(['read_at' => now()]);

        return $this->success(null, 'All notifications marked as read');
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $notification = InAppNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $notification->delete();

        return $this->noContent();
    }
}

