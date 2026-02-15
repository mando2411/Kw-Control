<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private array $data;

    public function __construct(array $data)
    {
        $this->subject = "Contact Request";
        $this->data = $data;
    }

    public function build(): self
    {
        return $this->markdown('emails.contact_email', [
            'name' => $this->data['name'],
            'email' => $this->data['email'],
            'subject' => $this->data['subject'],
            'phone' => $this->data['phone'],
            'message' => $this->data['message'],
        ]);
    }
}
