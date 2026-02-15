<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTailorTourNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello Admin,')
            ->subject('Tailor tour request')
            ->line('You have a new tailor tour request, find client information below.')
            ->line('Name: ' . $this->data['name'])
            ->line('Email: ' . $this->data['email'])
            ->line('Phone: ' . $this->data['phone'])
            ->line('Nationality: ' . $this->data['nationality'])
            ->line('Budget: ' . $this->data['budget'])
            ->line('Adults: ' . $this->data['adults'])
            ->line('Children: ' . $this->data['children'])
            ->line('Infants: ' . $this->data['infants'])
            ->line('Additional Notes: ' . $this->data['notes'])
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
