<!DOCTYPE html>
<html>
<head>
    <title>New Question Submitted</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #ea580c;">New Question Submitted</h2>
        
        <p>A new question has been submitted and requires your attention.</p>
        
        <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin-top: 0;">Customer Details:</h3>
            <p><strong>Name:</strong> {{ $question->name }}</p>
            <p><strong>Email:</strong> {{ $question->email }}</p>
            <p><strong>Phone:</strong> {{ $question->phone }}</p>
            <p><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($question->dob)->format('M d, Y') }}</p>
            @if($question->time)
                <p><strong>Time of Birth:</strong> {{ $question->time }}</p>
            @endif
            @if($question->place)
                <p><strong>Place of Birth:</strong> {{ $question->place }}</p>
            @endif
            <p><strong>Category:</strong> {{ ucfirst($question->category) }}</p>
            <p><strong>Submitted:</strong> {{ $question->created_at->format('M d, Y at h:i A') }}</p>
        </div>
        
        <div style="background: #fff; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px;">
            <h3>Question:</h3>
            <p>{{ $question->question }}</p>
        </div>
        
        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
            Please respond to this question within 24-48 hours.
        </p>
    </div>
</body>
</html>
