<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Str;

class ServiceEmailMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    private array $data;

    public function __construct(array $data)
    {
        $this->subject = "Book Service";
        $this->data = $data;
    }

    public function build(): self
    {
        return $this->markdown('emails.book_service_email', [
            'name' => $this->data['name'],
            'email' => $this->data['email'],
            'services' => collect($this->data['services'])->sort()->map(fn($service) => Str::headline($service))->implode(', '),
            'phone' => $this->data['phone'],
            'nationality' => $this->data['nationality'],
            'notes' => $this->data['notes'],
        ]);
    }
}
