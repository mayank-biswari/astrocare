<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: linear-gradient(135deg, #FF9933 0%, #FF6600 50%, #DC143C 100%); padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #FF9933 0%, #FF6600 50%, #DC143C 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .content { padding: 30px; }
        .booking-details { background: #FFF5E6; border-left: 4px solid #FF6600; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .booking-details h2 { color: #FF6600; margin-top: 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .detail-label { font-weight: bold; color: #666; }
        .detail-value { color: #333; }
        .amount { background: linear-gradient(135deg, #FF9933, #FF6600); color: white; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0; }
        .amount-label { font-size: 14px; opacity: 0.9; }
        .amount-value { font-size: 32px; font-weight: bold; margin: 5px 0; }
        .footer { background: #f8f8f8; padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .om-symbol { font-size: 24px; color: #FFD700; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="om-symbol">🕉️</div>
            <h1>Pooja Booking Confirmation</h1>
            <p>Your spiritual journey begins here</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $pooja->user->name }},</p>
            
            <p>Thank you for booking a pooja with us. Your booking has been confirmed successfully.</p>
            
            <div class="booking-details">
                <h2>Booking Details</h2>
                
                <div class="detail-row">
                    <span class="detail-label">Booking ID:</span>
                    <span class="detail-value">#{{ $pooja->id }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Pooja Name:</span>
                    <span class="detail-value">{{ $pooja->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Type:</span>
                    <span class="detail-value">{{ ucfirst($pooja->type) }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Scheduled Date:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($pooja->scheduled_at)->format('d M Y, h:i A') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">{{ ucfirst($pooja->status) }}</span>
                </div>
            </div>
            
            <div class="amount">
                <div class="amount-label">Total Amount</div>
                <div class="amount-value">{{ formatPrice($pooja->amount) }}</div>
            </div>
            
            @if($pooja->special_requirements)
            <div style="background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <strong>Special Requirements:</strong>
                <p style="margin: 5px 0 0 0;">{{ $pooja->special_requirements }}</p>
            </div>
            @endif
            
            <p style="margin-top: 30px;">Our experienced pandits will perform the ritual with authentic Vedic procedures. You will receive updates about your booking via email and SMS.</p>
            
            <p>For any queries, please contact us or visit your dashboard.</p>
            
            <p style="margin-top: 30px;">
                <strong>May the divine blessings be with you!</strong><br>
                Team {{ \App\Models\SiteSetting::get('site_name', 'AstroServices') }}
            </p>
        </div>
        
        <div class="footer">
            <div class="om-symbol">ॐ</div>
            <p>May the stars guide your path to prosperity</p>
            <p>&copy; {{ date('Y') }} {{ \App\Models\SiteSetting::get('site_name', 'AstroServices') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
