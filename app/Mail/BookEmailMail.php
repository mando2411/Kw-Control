<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookEmailMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build(): self
    {
        return $this->markdown('emails.book_tour_email', [
            'booking' => $this->booking,
        ])->subject('New Booking');
    }
}
