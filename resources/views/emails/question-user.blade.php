<!DOCTYPE html>
<html>
<head>
    <title>Question Submitted Successfully</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #ea580c;">Question Submitted Successfully</h2>
        
        <p>Dear {{ $question->name }},</p>
        
        <p>Thank you for submitting your question. Our expert astrologers will review your query and provide a detailed response within 24-48 hours.</p>
        
        <div style="background: #fff7ed; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ea580c;">
            <h3 style="margin-top: 0;">Your Question Details:</h3>
            <p><strong>Category:</strong> {{ ucfirst($question->category) }}</p>
            <p><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($question->dob)->format('M d, Y') }}</p>
            @if($question->time)
                <p><strong>Time of Birth:</strong> {{ $question->time }}</p>
            @endif
            @if($question->place)
                <p><strong>Place of Birth:</strong> {{ $question->place }}</p>
            @endif
            <p><strong>Question:</strong></p>
            <p style="background: white; padding: 15px; border-radius: 4px;">{{ $question->question }}</p>
        </div>
        
        <p>You will receive the answer via email at <strong>{{ $question->email }}</strong></p>
        
        <p style="margin-top: 30px;">Best regards,<br>{{ \App\Models\SiteSetting::get('site_name', 'AstroServices') }} Team</p>
    </div>
</body>
</html>
