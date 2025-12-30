<!DOCTYPE html>
<html>
<head>
    <title>New Contact Form Submission</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #4f46e5;">New Contact Form Submission</h2>
        
        <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p><strong>Name:</strong> {{ $submission->name }}</p>
            <p><strong>Email:</strong> {{ $submission->email }}</p>
            @if($submission->phone)
                <p><strong>Phone:</strong> {{ $submission->phone }}</p>
            @endif
            <p><strong>Subject:</strong> {{ $submission->subject }}</p>
            <p><strong>Submitted:</strong> {{ $submission->created_at->format('M d, Y at h:i A') }}</p>
        </div>
        
        <div style="background: #fff; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px;">
            <h3>Message:</h3>
            <p>{{ $submission->message }}</p>
        </div>
        
        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
            You can view and manage all contact submissions in your admin panel.
        </p>
    </div>
</body>
</html>