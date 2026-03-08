<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuestionSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $question;
    public $isAdmin;

    public function __construct($question, $isAdmin = false)
    {
        $this->question = $question;
        $this->isAdmin = $isAdmin;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isAdmin ? 'New Question Submitted - ' . $this->question->category : 'Question Submitted Successfully',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->isAdmin ? 'emails.question-admin' : 'emails.question-user',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
