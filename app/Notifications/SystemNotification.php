<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly array $payload = [])
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $data = $this->payload;
        $data['title'] = (string) ($data['title'] ?? 'إشعار جديد');
        $data['body'] = (string) ($data['body'] ?? '');
        $data['url'] = (string) ($data['url'] ?? '#');

        return $data;
    }
}
