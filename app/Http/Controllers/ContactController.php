<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactSubmission;
use App\Models\ContactSetting;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        $contactInfo = [
            'phone' => \App\Models\ContactSetting::get('contact_phone', '+91 98765 43210'),
            'email' => \App\Models\ContactSetting::get('admin_email', 'info@astrology.com'),
            'address' => \App\Models\ContactSetting::get('contact_address', "123 Astrology Street\nNew Delhi, India 110001"),
            'business_hours' => \App\Models\ContactSetting::get('business_hours', "Monday - Sunday\n9:00 AM - 9:00 PM"),
            'show_contact_info' => \App\Models\ContactSetting::get('show_contact_info', true)
        ];
        return view('contact', compact('contactInfo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'captcha' => 'required|captcha'
        ]);

        $submission = ContactSubmission::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Send email notification
        $adminEmail = ContactSetting::get('admin_email');
        if ($adminEmail) {
            try {
                Mail::send('emails.contact-notification', compact('submission'), function ($message) use ($adminEmail, $submission) {
                    $message->to($adminEmail)
                            ->subject('New Contact Form Submission: ' . $submission->subject);
                });
            } catch (\Exception $e) {
                // Log error but don't fail the submission
            }
        }

        // Create notification
        \App\Models\Notification::create(
            'contact',
            'New Contact Message',
            'New message from ' . $submission->name,
            ['contact_id' => $submission->id, 'url' => route('admin.contact.view', $submission->id)]
        );

        return redirect()->route('contact')->with('success', 'Thank you for your message. We will get back to you soon!');
    }
}