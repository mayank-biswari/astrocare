<?php

namespace App\Listeners;

use App\Events\PoojaBooked;
use App\Mail\PoojaBookingConfirmation;
use App\Models\ContactSetting;
use Illuminate\Support\Facades\Mail;

class SendPoojaBookingNotification
{
    public function handle(PoojaBooked $event): void
    {
        // Send to user
        Mail::to($event->pooja->user->email)->send(new PoojaBookingConfirmation($event->pooja));

        // Send to admin
        $adminEmail = ContactSetting::get('admin_email');
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new PoojaBookingConfirmation($event->pooja));
        }
    }
}
