<?php

namespace App\Mail;

use App\Models\Prediction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PredictionStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public Prediction $prediction;
    public string $newStatus;

    public function __construct(Prediction $prediction, string $newStatus)
    {
        $this->prediction = $prediction;
        $this->newStatus = $newStatus;
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->newStatus) {
            'processing' => 'Your ' . ucfirst($this->prediction->type) . ' Prediction is Being Prepared',
            'completed' => 'Your ' . ucfirst($this->prediction->type) . ' Prediction Report is Ready!',
            'cancelled' => 'Your ' . ucfirst($this->prediction->type) . ' Prediction Order Update',
            default => 'Prediction Status Update',
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.prediction-status-changed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
