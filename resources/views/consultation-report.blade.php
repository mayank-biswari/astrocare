<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Consultation Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table th, .info-table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .info-table th { background-color: #f5f5f5; }
        .notes { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Consultation Report</h1>
        <p>Generated on {{ now()->format('M d, Y g:i A') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <th>Consultation ID</th>
            <td>#{{ $consultation->id }}</td>
        </tr>
        <tr>
            <th>Type</th>
            <td>{{ ucfirst($consultation->type) }} Consultation</td>
        </tr>
        <tr>
            <th>Duration</th>
            <td>{{ $consultation->duration ?? 30 }} minutes</td>
        </tr>
        <tr>
            <th>Scheduled Date & Time</th>
            <td>{{ $consultation->scheduled_at ? $consultation->scheduled_at->format('M d, Y g:i A') : 'Not scheduled' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($consultation->status) }}</td>
        </tr>
        <tr>
            <th>Amount Paid</th>
            <td>₹{{ number_format($consultation->amount) }}</td>
        </tr>
        <tr>
            <th>Booking Date</th>
            <td>{{ $consultation->created_at->format('M d, Y g:i A') }}</td>
        </tr>
    </table>

    @if($consultation->notes)
    <div class="notes">
        <h3>Additional Notes</h3>
        <p>{{ $consultation->notes }}</p>
    </div>
    @endif

    @if($consultation->reschedule_reason)
    <div class="notes">
        <h3>Reschedule Reason</h3>
        <p>{{ $consultation->reschedule_reason }}</p>
    </div>
    @endif

    @if($consultation->suggestions)
    <div class="notes">
        <h3>Astrological Suggestions</h3>
        <p>{{ $consultation->suggestions }}</p>
    </div>
    @endif

    @if($consultation->remedies)
    <div class="notes">
        <h3>Recommended Remedies</h3>
        <p>{{ $consultation->remedies }}</p>
    </div>
    @endif

    <div style="margin-top: 40px; text-align: center; color: #666;">
        <p>Thank you for choosing our astrology consultation services!</p>
        <p>For any queries, please contact us at support@astroservices.com</p>
    </div>
</body>
</html>