<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = min((int) $request->get('limit', 20), 50);

        $notifications = $user->notifications()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (DatabaseNotification $n) => $this->formatNotification($n));

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if ($notification && ! $notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json([
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['unread_count' => 0]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatNotification(DatabaseNotification $notification): array
    {
        $data = $notification->data;
        $type = $data['type'] ?? 'unknown';
        $params = [
            'record' => $data['receiving_record_no'] ?? '',
            'batch' => $data['batch_id'] ?? '',
        ];

        $locale = auth()->user()?->preferredLocale() ?? app()->getLocale();
        $message = __("notifications.{$type}", $params, $locale);

        return [
            'id' => $notification->id,
            'type' => $type,
            'message' => $message,
            'receiving_record_no' => $data['receiving_record_no'] ?? null,
            'test_request_id' => $data['test_request_id'] ?? null,
            'actor_name' => $data['actor_name'] ?? null,
            'read_at' => $notification->read_at?->toIso8601String(),
            'created_at' => $notification->created_at?->toIso8601String(),
            'created_at_human' => $notification->created_at?->diffForHumans(),
        ];
    }
}
