<!DOCTYPE html>
<html>
<head>
    <title>New Campaign Lead</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #4f46e5;">New Campaign Lead</h2>

        <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p><strong>Full Name:</strong> {{ $lead->full_name }}</p>
            <p><strong>Email:</strong> {{ $lead->email }}</p>
            <p><strong>Phone Number:</strong> {{ $lead->phone_number }}</p>
            <p><strong>Date of Birth:</strong> {{ $lead->date_of_birth->format('d M Y') }}</p>
            <p><strong>Place of Birth:</strong> {{ $lead->place_of_birth }}</p>
            <p><strong>Campaign Source:</strong> {{ $lead->source }}</p>
            <p><strong>Lead Code:</strong> {{ $lead->lead_code }}</p>
        </div>

        @if($lead->message)
        <div style="background: #fff; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px;">
            <h3>Message:</h3>
            <p>{{ $lead->message }}</p>
        </div>
        @endif

        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
            This lead was submitted via the campaign form. You can view and manage all leads in your admin panel.
        </p>
    </div>
</body>
</html>
