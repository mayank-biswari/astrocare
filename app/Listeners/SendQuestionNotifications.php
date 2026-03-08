<?php

namespace App\Listeners;

use App\Events\QuestionSubmitted;
use App\Mail\QuestionSubmitted as QuestionSubmittedMail;
use App\Models\ContactSetting;
use Illuminate\Support\Facades\Mail;

class SendQuestionNotifications
{
    public function handle(QuestionSubmitted $event)
    {
        $question = $event->question;

        // Send email to user
        Mail::to($question->email)->send(new QuestionSubmittedMail($question, false));

        // Send email to admin
        $adminEmail = ContactSetting::get('email');
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new QuestionSubmittedMail($question, true));
        }
    }
}
