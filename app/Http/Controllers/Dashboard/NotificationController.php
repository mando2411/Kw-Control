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

        $user->unreadNotifications->each(function ($notification) {
            if (!$this->isLockedUntilDecision($notification)) {
                $notification->markAsRead();
            }
        });

        $notifications = $user->notifications()
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotifications()->count(),
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
                    'url' => (string) ($data['url'] ?? $safePageUrl),
                    'kind' => (string) ($data['kind'] ?? ''),
                    'join_request_id' => (int) ($data['join_request_id'] ?? 0),
                    'lock_read_until_decision' => (bool) ($data['lock_read_until_decision'] ?? false),
                    'decision' => (string) ($data['decision'] ?? ''),
                    'decision_closed' => (bool) ($data['decision_closed'] ?? false),
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

        if ($this->isLockedUntilDecision($notification)) {
            return response()->json([
                'success' => true,
                'unread_count' => $request->user()->unreadNotifications()->count(),
                'locked' => true,
            ]);
        }

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
        $request->user()->unreadNotifications->each(function ($notification) {
            if (!$this->isLockedUntilDecision($notification)) {
                $notification->markAsRead();
            }
        });

        return response()->json([
            'success' => true,
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    private function isLockedUntilDecision($notification): bool
    {
        $data = is_array($notification->data) ? $notification->data : [];
        $locked = (bool) ($data['lock_read_until_decision'] ?? false);
        $decision = (string) ($data['decision'] ?? 'pending');

        return $locked && $decision === 'pending';
    }
}
