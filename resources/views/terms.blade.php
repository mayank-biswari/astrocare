@extends('layouts.app')

@section('title', 'Terms and Conditions - AstroServices')

@section('content')
<div class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-4">Terms and Conditions</h1>
        <p class="text-xl">Please read these terms carefully before using our services</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <p class="text-gray-600 mb-6">Last updated: {{ date('F d, Y') }}</p>

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">1. Acceptance of Terms</h2>
            <p class="text-gray-600 mb-4">
                By accessing and using AstroServices, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to these terms, please do not use our services.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">2. Services Description</h2>
            <p class="text-gray-600 mb-4">
                AstroServices provides astrology consultations, kundli generation, pooja booking services, and spiritual products. All services are provided for informational and spiritual guidance purposes only.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">3. User Responsibilities</h2>
            <ul class="list-disc list-inside text-gray-600 space-y-2">
                <li>You must provide accurate and complete information when using our services</li>
                <li>You are responsible for maintaining the confidentiality of your account</li>
                <li>You must not use our services for any illegal or unauthorized purpose</li>
                <li>You must not interfere with or disrupt our services or servers</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">4. Consultations and Predictions</h2>
            <p class="text-gray-600 mb-4">
                All astrological consultations and predictions are based on traditional Vedic astrology principles. Results may vary and are not guaranteed. Consultations should not be considered as professional medical, legal, or financial advice.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">5. Payment Terms</h2>
            <ul class="list-disc list-inside text-gray-600 space-y-2">
                <li>All prices are displayed in the selected currency</li>
                <li>Payment must be made in full before services are rendered</li>
                <li>We accept payments through authorized payment gateways only</li>
                <li>All transactions are secure and encrypted</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">6. Refund and Cancellation Policy</h2>
            <p class="text-gray-600 mb-4">
                Cancellations must be made at least 24 hours before the scheduled consultation. Refunds will be processed within 7-10 business days. Products can be returned within 7 days of delivery if unused and in original packaging.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">7. Privacy and Data Protection</h2>
            <p class="text-gray-600 mb-4">
                We respect your privacy and protect your personal information. All data collected is used solely for providing services and will not be shared with third parties without your consent.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">8. Intellectual Property</h2>
            <p class="text-gray-600 mb-4">
                All content, including text, graphics, logos, and software, is the property of AstroServices and protected by copyright laws. Unauthorized use is prohibited.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">9. Limitation of Liability</h2>
            <p class="text-gray-600 mb-4">
                AstroServices shall not be liable for any indirect, incidental, special, or consequential damages arising from the use of our services. Our liability is limited to the amount paid for the specific service.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">10. Modifications to Terms</h2>
            <p class="text-gray-600 mb-4">
                We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting. Continued use of our services constitutes acceptance of modified terms.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">11. Contact Information</h2>
            <p class="text-gray-600 mb-4">
                For questions about these terms, please contact us at:
            </p>
            <ul class="text-gray-600 space-y-2">
                <li>Email: support@astroservices.com</li>
                <li>Phone: +1 (555) 123-4567</li>
                <li>Address: 123 Spiritual Lane, Astro City, AC 12345</li>
            </ul>
        </section>

        <div class="border-t pt-6 mt-8">
            <p class="text-gray-600 text-center">
                By using AstroServices, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.
            </p>
        </div>
    </div>
</div>
@endsection
