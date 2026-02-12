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
        return [
            'title' => (string) ($this->payload['title'] ?? 'إشعار جديد'),
            'body' => (string) ($this->payload['body'] ?? ''),
            'url' => (string) ($this->payload['url'] ?? '#'),
        ];
    }
}
