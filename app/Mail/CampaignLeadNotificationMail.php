<?php

namespace App\Mail;

use App\Models\CampaignLead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignLeadNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CampaignLead $lead
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Campaign Lead - {$this->lead->source}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign-lead-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
