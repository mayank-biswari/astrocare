<?php

namespace App\Listeners;

use App\Events\ContactFormSubmitted;
use App\Models\ContactSetting;
use Illuminate\Support\Facades\Mail;

class SendContactNotification
{
    public function handle(ContactFormSubmitted $event): void
    {
        $adminEmail = ContactSetting::get('admin_email');
        if ($adminEmail) {
            Mail::send('emails.contact-notification', ['submission' => $event->submission], function ($message) use ($adminEmail, $event) {
                $message->to($adminEmail)
                        ->subject('New Contact Form Submission: ' . $event->submission->subject);
            });
        }
    }
}
