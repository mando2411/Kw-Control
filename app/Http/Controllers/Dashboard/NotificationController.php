<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function page(Request $request): View
    {
        $user = $request->user();

        $user->unreadNotifications()->update([
            'read_at' => now(),
        ]);

        $notifications = $user->notifications()
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => 0,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = $user->notifications()
            ->latest()
            ->limit(12)
            ->get()
            ->map(function ($notification) {
                $data = is_array($notification->data) ? $notification->data : [];
                $safePageUrl = route('dashboard.notifications.page', ['open' => $notification->id]);

                return [
                    'id' => $notification->id,
                    'title' => (string) ($data['title'] ?? 'إشعار جديد'),
                    'body' => (string) ($data['body'] ?? ''),
                    'url' => $safePageUrl,
                    'read_at' => $notification->read_at,
                    'created_at' => optional($notification->created_at)->diffForHumans(),
                ];
            })
            ->values();

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'items' => $items,
        ]);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}
