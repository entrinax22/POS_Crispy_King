<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class NotificationController extends Controller
{   

    public function index()
    {
        return Inertia::render('notifications/Index');
    }
    
    /**
     * Display a listing of the notifications.
     */
    public function list()
    {
        $notifications = auth()->user()->notifications()->paginate(10);
        $data = $notifications->getCollection()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->data['title'],
                'message' => $notification->data['message'],
                'order_id' => $notification->data['order_id'] ?? null,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
            ];
        });
        return response()->json([
            'result' => true,
            'message' => 'Notifications retrieved successfully.',
            'data' => $data,
            'pagination' => [
                'total' => $notifications->total(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
            ],
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'result' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json([
            'result' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    public function unreadCount()
    {
        $count = auth()->user()->unreadNotifications->count();
        return response()->json([
            'result' => true,
            'message' => 'Unread notifications count retrieved successfully.',
            'count' => $count,
        ]);
    }
}
