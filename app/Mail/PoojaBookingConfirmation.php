<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PoojaBookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $pooja;

    public function __construct($pooja)
    {
        $this->pooja = $pooja;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pooja Booking Confirmation - ' . $this->pooja->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pooja-booking',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
