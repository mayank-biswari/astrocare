<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #FF9933, #DC143C); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #fff; border: 1px solid #eee; padding: 30px; border-radius: 0 0 8px 8px; }
        .status-badge { display: inline-block; padding: 6px 16px; border-radius: 20px; font-weight: bold; font-size: 14px; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .report-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        .btn { display: inline-block; background: linear-gradient(135deg, #FF9933, #DC143C); color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">{{ \App\Models\SiteSetting::get('site_name', 'AstroServices') }}</h1>
    </div>
    <div class="content">
        <h2>Hello {{ $prediction->name }},</h2>

        <p>Your <strong>{{ ucfirst($prediction->type) }} Prediction</strong> order has been updated.</p>

        <p>
            <strong>New Status:</strong>
            <span class="status-badge status-{{ $newStatus }}">{{ ucfirst($newStatus) }}</span>
        </p>

        @if($newStatus === 'processing')
            <p>Our astrologer is currently preparing your personalized {{ $prediction->type }} prediction report. You will be notified once it's ready.</p>
        @elseif($newStatus === 'completed')
            <p>Great news! Your prediction report is now ready. You can view it in your dashboard.</p>

            @if($prediction->report)
                <div class="report-box">
                    <h3 style="margin-top: 0;">Your Prediction Report</h3>
                    <p>{!! nl2br(e($prediction->report)) !!}</p>
                </div>
            @endif

            <p style="text-align: center;">
                <a href="{{ url('/dashboard/predictions') }}" class="btn">View in Dashboard</a>
            </p>
        @elseif($newStatus === 'cancelled')
            <p>Unfortunately, your prediction order has been cancelled. If you have any questions, please contact our support team.</p>
        @endif

        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">

        <p><strong>Order Details:</strong></p>
        <ul>
            <li>Type: {{ ucfirst($prediction->type) }} Prediction</li>
            <li>Amount: ₹{{ number_format($prediction->amount, 2) }}</li>
            <li>Ordered: {{ $prediction->created_at->format('M d, Y') }}</li>
        </ul>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} {{ \App\Models\SiteSetting::get('site_name', 'AstroServices') }}. All rights reserved.</p>
        <p>This is an automated email. Please do not reply directly.</p>
    </div>
</body>
</html>
