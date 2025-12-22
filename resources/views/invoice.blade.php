<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $orderId }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4F46E5; padding-bottom: 20px; }
        .company-name { font-size: 28px; font-weight: bold; color: #4F46E5; margin-bottom: 5px; }
        .company-tagline { color: #666; font-size: 14px; }
        .invoice-details { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .invoice-info, .customer-info { width: 48%; }
        .invoice-info h3, .customer-info h3 { color: #4F46E5; margin-bottom: 10px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .items-table th { background-color: #4F46E5; color: white; }
        .total-section { text-align: right; margin-top: 20px; }
        .total-row { margin: 5px 0; }
        .grand-total { font-size: 18px; font-weight: bold; color: #4F46E5; border-top: 2px solid #4F46E5; padding-top: 10px; }
        .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">🔮 AstroServices</div>
        <div class="company-tagline">Your trusted partner for astrology services and spiritual products</div>
    </div>

    <div class="invoice-details">
        <div class="invoice-info">
            <h3>Invoice Details</h3>
            <p><strong>Invoice #:</strong> INV-{{ $orderId }}</p>
            <p><strong>Order #:</strong> {{ $orderId }}</p>
            <p><strong>Date:</strong> {{ date('F d, Y') }}</p>
            <p><strong>Payment Method:</strong> Cash on Delivery</p>
        </div>
        <div class="customer-info">
            <h3>Bill To</h3>
            <p><strong>John Doe</strong></p>
            <p>123 Main Street<br>Apartment 4B<br>Mumbai, Maharashtra 400001</p>
            <p>Phone: +91 9876543210</p>
            <p>Email: john@example.com</p>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Shri Yantra</td>
                <td>Copper, 3x3 inches, energized</td>
                <td>1</td>
                <td>₹899</td>
                <td>₹899</td>
            </tr>
            <tr>
                <td>5 Mukhi Rudraksha</td>
                <td>Nepali origin, lab certified</td>
                <td>1</td>
                <td>₹299</td>
                <td>₹299</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">Subtotal: ₹1,198</div>
        <div class="total-row">Shipping: <span style="color: green;">Free</span></div>
        <div class="total-row">Tax: ₹0</div>
        <div class="total-row grand-total">Grand Total: ₹1,198</div>
    </div>

    <div class="footer">
        <p><strong>Thank you for your business!</strong></p>
        <p>AstroServices | Email: info@astroservices.com | Phone: +91 9876543210</p>
        <p>For any queries regarding this invoice, please contact our customer support.</p>
    </div>
</body>
</html>