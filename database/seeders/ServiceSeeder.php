<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceTier;
use App\Models\ServiceFormField;

class ServiceSeeder extends Seeder
{
    /**
     * Seed existing services into the dynamic services management system.
     * Uses updateOrCreate with slug as key for idempotency.
     */
    public function run(): void
    {
        $this->seedAskAQuestion();
        $this->seedMonthlyPrediction();
        $this->seedYearlyPrediction();
        $this->seedKundliGeneration();
        $this->seedHoroscopeMatching();
        $this->seedConsultation();
        $this->seedPoojaBooking();
    }

    private function seedAskAQuestion(): void
    {
        $service = Service::updateOrCreate(
            ['slug' => 'ask-a-question'],
            [
                'name' => 'Ask a Question',
                'type' => 'question',
                'short_description' => 'Get expert astrological guidance for your life questions.',
                'description' => 'Get personalized astrological insights and practical remedies for your life\'s most important questions. Our expert astrologers provide detailed written responses within 24-48 hours.',
                'icon' => 'fas fa-question-circle',
                'base_price' => 499.00,
                'currency' => 'INR',
                'has_tiers' => false,
                'features' => [
                    'Detailed written response within 24-48 hours',
                    'Personalized astrological analysis based on your birth chart',
                    'Practical remedies and actionable suggestions',
                    'Follow-up support via email for clarifications',
                ],
                'faq' => [
                    ['question' => 'How long does it take to get an answer?', 'answer' => 'You will receive a detailed written response within 24-48 hours of submitting your question.'],
                    ['question' => 'Can I ask multiple questions?', 'answer' => 'Each submission covers one main question. For multiple questions, please submit separate forms.'],
                    ['question' => 'Do I need exact birth time?', 'answer' => 'While exact birth time helps provide more accurate predictions, we can still provide guidance with approximate time or date only.'],
                    ['question' => 'Is my information confidential?', 'answer' => 'Yes, all your personal information and questions are kept strictly confidential and secure.'],
                ],
                'meta_title' => 'Ask a Question to Expert Astrologer | Get Personalized Astrological Guidance',
                'meta_description' => 'Get expert astrological guidance for your life questions. Ask about career, love, marriage, health, finance. Detailed written response within 24-48 hours. ₹499 only.',
                'meta_keywords' => 'ask astrologer, astrology question, astrological guidance, career astrology, love astrology, marriage prediction',
                'requires_auth' => true,
                'requires_captcha' => true,
                'delivery_time' => '24-48 hours',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $this->createFormFields($service, [
            ['field_name' => 'name', 'field_label' => 'Full Name', 'field_type' => 'text', 'placeholder' => 'Enter your full name', 'validation_rules' => 'required|string|max:255', 'is_required' => true, 'section' => 'personal', 'section_label' => 'Personal Details', 'sort_order' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'field_type' => 'email', 'placeholder' => 'Enter your email', 'validation_rules' => 'required|email', 'is_required' => true, 'section' => 'personal', 'sort_order' => 2],
            ['field_name' => 'phone', 'field_label' => 'Phone Number', 'field_type' => 'tel', 'placeholder' => 'Enter your phone number', 'validation_rules' => 'required|string', 'is_required' => true, 'section' => 'personal', 'sort_order' => 3],
            ['field_name' => 'dob', 'field_label' => 'Date of Birth', 'field_type' => 'date', 'validation_rules' => 'required|date', 'is_required' => true, 'section' => 'birth', 'section_label' => 'Birth Details', 'sort_order' => 4],
            ['field_name' => 'time', 'field_label' => 'Time of Birth', 'field_type' => 'time', 'validation_rules' => 'nullable|string', 'is_required' => false, 'section' => 'birth', 'sort_order' => 5, 'help_text' => 'Approximate time is acceptable if exact time is unknown'],
            ['field_name' => 'place', 'field_label' => 'Place of Birth', 'field_type' => 'text', 'placeholder' => 'Enter your birth place', 'validation_rules' => 'nullable|string|max:255', 'is_required' => false, 'section' => 'birth', 'sort_order' => 6],
            ['field_name' => 'category', 'field_label' => 'Question Category', 'field_type' => 'select', 'validation_rules' => 'required|string', 'is_required' => true, 'section' => 'question', 'section_label' => 'Your Question', 'sort_order' => 7, 'options' => [
                ['value' => 'career', 'label' => 'Career & Business'],
                ['value' => 'love', 'label' => 'Love & Relationships'],
                ['value' => 'marriage', 'label' => 'Marriage & Family'],
                ['value' => 'health', 'label' => 'Health & Wellness'],
                ['value' => 'finance', 'label' => 'Finance & Money'],
                ['value' => 'education', 'label' => 'Education & Studies'],
                ['value' => 'general', 'label' => 'General Life Guidance'],
            ]],
            ['field_name' => 'question', 'field_label' => 'Your Question', 'field_type' => 'textarea', 'placeholder' => 'Please describe your question in detail. The more specific you are, the better guidance we can provide.', 'validation_rules' => 'required|string', 'is_required' => true, 'section' => 'question', 'sort_order' => 8],
        ]);
    }

    private function seedMonthlyPrediction(): void
    {
        $service = Service::updateOrCreate(
            ['slug' => 'monthly-prediction'],
            [
                'name' => 'Monthly Prediction',
                'type' => 'prediction',
                'short_description' => 'Detailed monthly predictions and guidance based on your birth chart.',
                'description' => 'Get a comprehensive monthly forecast covering career, relationships, health, and finances. Our expert astrologers analyze your birth chart to provide personalized predictions for the month ahead.',
                'icon' => 'fas fa-chart-line',
                'base_price' => 299.00,
                'currency' => 'INR',
                'has_tiers' => false,
                'features' => [
                    'Personalized monthly forecast based on your birth chart',
                    'Career, love, health, and finance predictions',
                    'Lucky dates and favorable periods highlighted',
                    'Remedies and suggestions for the month',
                ],
                'faq' => [
                    ['question' => 'How accurate are monthly predictions?', 'answer' => 'Our predictions are based on Vedic astrology principles and your exact birth chart. While astrology provides guidance, outcomes also depend on personal choices and actions.'],
                    ['question' => 'When will I receive my report?', 'answer' => 'You will receive your detailed monthly prediction report within 24-48 hours of payment confirmation.'],
                    ['question' => 'Can I get predictions for a specific month?', 'answer' => 'Yes, you can request predictions for any upcoming month. By default, we provide predictions for the current/next month.'],
                ],
                'meta_title' => 'Monthly Astrological Predictions | Personalized Monthly Forecast',
                'meta_description' => 'Get detailed monthly predictions based on your birth chart. Career, love, health, and finance forecasts with remedies. ₹299 only.',
                'meta_keywords' => 'monthly prediction, monthly horoscope, astrology forecast, monthly astrology report',
                'requires_auth' => true,
                'requires_captcha' => false,
                'delivery_time' => '24-48 hours',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $this->createFormFields($service, [
            ['field_name' => 'name', 'field_label' => 'Full Name', 'field_type' => 'text', 'placeholder' => 'Enter your full name', 'validation_rules' => 'required|string|max:255', 'is_required' => true, 'section' => 'personal', 'section_label' => 'Personal Details', 'sort_order' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'field_type' => 'email', 'placeholder' => 'Enter your email', 'validation_rules' => 'required|email', 'is_required' => true, 'section' => 'personal', 'sort_order' => 2],
            ['field_name' => 'dob', 'field_label' => 'Date of Birth', 'field_type' => 'date', 'validation_rules' => 'required|date', 'is_required' => true, 'section' => 'birth', 'section_label' => 'Birth Details', 'sort_order' => 3],
            ['field_name' => 'time', 'field_label' => 'Time of Birth', 'field_type' => 'time', 'validation_rules' => 'nullable|string', 'is_required' => false, 'section' => 'birth', 'sort_order' => 4, 'help_text' => 'Providing birth time improves prediction accuracy'],
        ]);
    }

    private function seedYearlyPrediction(): void
    {
        $service = Service::updateOrCreate(
            ['slug' => 'yearly-prediction'],
            [
                'name' => 'Yearly Prediction',
                'type' => 'prediction',
                'short_description' => 'Complete year ahead analysis with detailed predictions and remedies.',
                'description' => 'Get a comprehensive yearly forecast with month-by-month breakdown. Our expert astrologers provide detailed analysis of career prospects, relationship dynamics, health outlook, and financial opportunities for the entire year.',
                'icon' => 'fas fa-calendar-alt',
                'base_price' => 999.00,
                'currency' => 'INR',
                'has_tiers' => false,
                'features' => [
                    'Complete year ahead analysis with month-by-month breakdown',
                    'Career, love, health, and finance predictions for the full year',
                    'Major planetary transits and their impact on your life',
                    'Personalized remedies and guidance for challenging periods',
                    'Lucky periods and auspicious dates highlighted',
                ],
                'faq' => [
                    ['question' => 'What does the yearly prediction cover?', 'answer' => 'It covers all major life areas including career, relationships, health, finances, education, and spiritual growth with month-by-month analysis.'],
                    ['question' => 'How is this different from monthly predictions?', 'answer' => 'Yearly predictions provide a broader overview of the entire year with major transit effects, while monthly predictions focus on detailed day-to-day guidance for a single month.'],
                    ['question' => 'Do I need my exact birth place?', 'answer' => 'Yes, birth place is required for yearly predictions as it helps determine your ascendant and house placements accurately.'],
                ],
                'meta_title' => 'Yearly Astrological Predictions | Complete Year Ahead Analysis',
                'meta_description' => 'Get comprehensive yearly predictions with month-by-month breakdown. Career, love, health forecasts with remedies. ₹999 only.',
                'meta_keywords' => 'yearly prediction, annual horoscope, year ahead astrology, yearly forecast',
                'requires_auth' => true,
                'requires_captcha' => false,
                'delivery_time' => '48-72 hours',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        $this->createFormFields($service, [
            ['field_name' => 'name', 'field_label' => 'Full Name', 'field_type' => 'text', 'placeholder' => 'Enter your full name', 'validation_rules' => 'required|string|max:255', 'is_required' => true, 'section' => 'personal', 'section_label' => 'Personal Details', 'sort_order' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'field_type' => 'email', 'placeholder' => 'Enter your email', 'validation_rules' => 'required|email', 'is_required' => true, 'section' => 'personal', 'sort_order' => 2],
            ['field_name' => 'dob', 'field_label' => 'Date of Birth', 'field_type' => 'date', 'validation_rules' => 'required|date', 'is_required' => true, 'section' => 'birth', 'section_label' => 'Birth Details', 'sort_order' => 3],
            ['field_name' => 'time', 'field_label' => 'Time of Birth', 'field_type' => 'time', 'validation_rules' => 'nullable|string', 'is_required' => false, 'section' => 'birth', 'sort_order' => 4],
            ['field_name' => 'place', 'field_label' => 'Place of Birth', 'field_type' => 'text', 'placeholder' => 'Enter your birth place', 'validation_rules' => 'required|string|max:255', 'is_required' => true, 'section' => 'birth', 'sort_order' => 5],
        ]);
    }

    private function seedKundliGeneration(): void
    {
        $service = Service::updateOrCreate(
            ['slug' => 'kundli-generation'],
            [
                'name' => 'Kundli Generation',
                'type' => 'kundli',
                'short_description' => 'Discover your destiny through detailed birth chart analysis.',
                'description' => 'Get a comprehensive Kundli (birth chart) generated by expert astrologers. Choose from Basic, Detailed, or Premium packages based on the depth of analysis you need.',
                'icon' => 'fas fa-om',
                'base_price' => 299.00,
                'currency' => 'INR',
                'has_tiers' => true,
                'features' => [
                    'Accurate birth chart with planetary positions',
                    'Dasha period analysis and predictions',
                    'Personalized remedies and gemstone recommendations',
                    'Career, marriage, and health analysis',
                    'PDF report delivered to your email',
                ],
                'faq' => [
                    ['question' => 'What is a Kundli?', 'answer' => 'A Kundli (also called birth chart or natal chart) is a map of the sky at the exact time and place of your birth. It shows the positions of planets and their influence on your life.'],
                    ['question' => 'What details do I need to provide?', 'answer' => 'You need your full name, exact date of birth, time of birth (as accurate as possible), and place of birth.'],
                    ['question' => 'What is the difference between Basic, Detailed, and Premium?', 'answer' => 'Basic includes birth chart and planetary positions. Detailed adds Dasha periods and remedies. Premium includes complete life analysis with yearly predictions.'],
                    ['question' => 'How will I receive my Kundli?', 'answer' => 'Your Kundli report will be delivered as a PDF to your registered email and will also be available for download from your dashboard.'],
                ],
                'meta_title' => 'Kundli Generation | Birth Chart Analysis by Expert Astrologers',
                'meta_description' => 'Get your Kundli generated by expert astrologers. Basic, Detailed, and Premium packages available. Accurate birth chart analysis starting at ₹299.',
                'meta_keywords' => 'kundli generation, birth chart, natal chart, kundli reading, janam kundli',
                'requires_auth' => true,
                'requires_captcha' => false,
                'delivery_time' => '24-48 hours',
                'is_active' => true,
                'sort_order' => 4,
            ]
        );

        // Create tiers
        ServiceTier::updateOrCreate(
            ['service_id' => $service->id, 'slug' => 'basic'],
            [
                'name' => 'Basic Kundli',
                'description' => 'Birth chart with planetary positions and basic predictions.',
                'price' => 299.00,
                'currency' => 'INR',
                'features' => ['Birth chart', 'Planetary positions', 'Basic predictions'],
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        ServiceTier::updateOrCreate(
            ['service_id' => $service->id, 'slug' => 'detailed'],
            [
                'name' => 'Detailed Kundli',
                'description' => 'Comprehensive analysis with Dasha periods and remedies.',
                'price' => 599.00,
                'currency' => 'INR',
                'features' => ['Detailed analysis', 'Dasha periods', 'Remedies', 'Career guidance'],
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        ServiceTier::updateOrCreate(
            ['service_id' => $service->id, 'slug' => 'premium'],
            [
                'name' => 'Premium Kundli',
                'description' => 'Complete life analysis with yearly predictions and guidance.',
                'price' => 999.00,
                'currency' => 'INR',
                'features' => ['Complete analysis', 'Yearly predictions', 'Marriage compatibility', 'Health analysis', 'Gemstone recommendations'],
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $this->createFormFields($service, [
            ['field_name' => 'name', 'field_label' => 'Full Name', 'field_type' => 'text', 'placeholder' => 'Enter your full name', 'validation_rules' => 'required|string|max:255', 'is_required' => true, 'section' => 'personal', 'section_label' => 'Personal Details', 'sort_order' => 1],
            ['field_name' => 'birth_date', 'field_label' => 'Date of Birth', 'field_type' => 'date', 'validation_rules' => 'required|date', 'is_required' => true, 'section' => 'birth', 'section_label' => 'Birth Details', 'sort_order' => 2],
            ['field_name' => 'birth_time', 'field_label' => 'Time of Birth', 'field_type' => 'time', 'validation_rules' => 'required|string', 'is_required' => true, 'section' => 'birth', 'sort_order' => 3, 'help_text' => 'Please provide as accurate a time as possible'],
            ['field_name' => 'birth_place', 'field_label' => 'Place of Birth', 'field_type' => 'text', 'placeholder' => 'Enter your birth place', 'validation_rules' => 'required|string|max:255', 'is_required' => true, 'section' => 'birth', 'sort_order' => 4],
        ]);
    }

    private function seedHoroscopeMatching(): void
    {
        $service = Service::updateOrCreate(
            ['slug' => 'horoscope-matching'],
            [
                'name' => 'Horoscope Matching',
                'type' => 'matching',
                'short_description' => 'Find your perfect match through Vedic astrology compatibility analysis.',
                'description' => 'Get a comprehensive horoscope matching (Kundli Milan) report based on Vedic astrology. Our analysis includes 36 Guna matching, Mangal Dosha check, and detailed compatibility assessment with remedies.',
                'icon' => 'fas fa-heart',
                'base_price' => 999.00,
                'currency' => 'INR',
                'has_tiers' => false,
                'features' => [
                    '36 Guna Matching (Ashtakoot analysis)',
                    'Mangal Dosha check with detailed analysis',
                    'Comprehensive compatibility report with remedies',
                    'Nadi Dosha and other important checks',
                    'Expert recommendations for marriage compatibility',
                ],
                'faq' => [
                    ['question' => 'What is 36 Guna Matching?', 'answer' => 'Ashtakoot matching evaluates 8 aspects (Gunas) of compatibility between two horoscopes, with a maximum score of 36. A score above 18 is generally considered favorable.'],
                    ['question' => 'What is Mangal Dosha?', 'answer' => 'Mangal Dosha occurs when Mars is placed in certain houses of the birth chart. It can affect marriage compatibility, but remedies are available.'],
                    ['question' => 'Do I need exact birth times for both partners?', 'answer' => 'Yes, accurate birth times for both partners are essential for precise horoscope matching and compatibility analysis.'],
                    ['question' => 'How long does it take to get the report?', 'answer' => 'You will receive your detailed compatibility report within 24-48 hours of submission.'],
                ],
                'meta_title' => 'Horoscope Matching | Kundli Milan | Marriage Compatibility',
                'meta_description' => 'Get comprehensive horoscope matching with 36 Guna analysis, Mangal Dosha check, and compatibility report. Expert Vedic astrology matching for ₹999.',
                'meta_keywords' => 'horoscope matching, kundli milan, marriage compatibility, guna matching, mangal dosha',
                'requires_auth' => true,
                'requires_captcha' => true,
                'delivery_time' => '24-48 hours',
                'is_active' => true,
                'sort_order' => 5,
            ]
        );

        $this->createFormFields($service, [
            ['field_name' => 'male_name', 'field_label' => 'Male Full Name', 'field_type' => 'text', 'placeholder' => 'Enter male\'s full name', 'validation_rules' => 'required|string|max:255', 'is_required' => true, 'section' => 'male', 'section_label' => 'Male Details', 'sort_order' => 1],
            ['field_name' => 'male_dob', 'field_label' => 'Male Date of Birth', 'field_type' => 'date', 'validation_rules' => 'required|date', 'is_required' => true, 'section' => 'male', 'sort_order' => 2],
            ['field_name' => 'male_time', 'field_label' => 'Male Time of Birth', 'field_type' => 'time', 'validation_rules' => 'required|string', 'is_required' => true, 'section' => 'male', 'sort_order' => 3],
            ['field_name' => 'male_place', 'field_label' => 'Male Place of Birth', 'field_type' => 'text', 'placeholder' => 'Enter male\'s birth place', 'validation_rules' => 'required|string|max:255', 'is_required' => true, 'section' => 'male', 'sort_order' => 4],
            ['field_name' => 'female_name', 'field_label' => 'Female Full Name', 'field_type' => 'text', 'placeholder' => 'Enter female\'s full name', 'validation_rules' => 'required|string|max:255', 'is_required' => true, 'section' => 'female', 'section_label' => 'Female Details', 'sort_order' => 5],
            ['field_name' => 'female_dob', 'field_label' => 'Female Date of Birth', 'field_type' => 'date', 'validation_rules' => 'required|date', 'is_required' => true, 'section' => 'female', 'sort_order' => 6],
            ['field_name' => 'female_time', 'field_label' => 'Female Time of Birth', 'field_type' => 'time', 'validation_rules' => 'required|string', 'is_required' => true, 'section' => 'female', 'sort_order' => 7],
            ['field_name' => 'female_place', 'field_label' => 'Female Place of Birth', 'field_type' => 'text', 'placeholder' => 'Enter female\'s birth place', 'validation_rules' => 'required|string|max:255', 'is_required' => true, 'section' => 'female', 'sort_order' => 8],
            ['field_name' => 'email', 'field_label' => 'Email', 'field_type' => 'email', 'placeholder' => 'Enter your email', 'validation_rules' => 'required|email', 'is_required' => true, 'section' => 'contact', 'section_label' => 'Contact Details', 'sort_order' => 9],
            ['field_name' => 'phone', 'field_label' => 'Phone Number', 'field_type' => 'tel', 'placeholder' => 'Enter your phone number', 'validation_rules' => 'required|string', 'is_required' => true, 'section' => 'contact', 'sort_order' => 10],
        ]);
    }

    private function seedConsultation(): void
    {
        $service = Service::updateOrCreate(
            ['slug' => 'consultation'],
            [
                'name' => 'Consultation',
                'type' => 'consultation',
                'short_description' => 'Get personalized guidance from expert astrologers via phone, video, or chat.',
                'description' => 'Book a one-on-one consultation with our expert astrologers. Choose your preferred duration and get personalized guidance on career, relationships, health, and life decisions.',
                'icon' => 'fas fa-phone-alt',
                'base_price' => 499.00,
                'currency' => 'INR',
                'has_tiers' => true,
                'features' => [
                    'One-on-one session with expert astrologer',
                    'Personalized guidance based on your birth chart',
                    'Career, relationship, and life decision advice',
                    'Remedies and actionable suggestions',
                    'Follow-up support after consultation',
                ],
                'faq' => [
                    ['question' => 'How do I join the consultation?', 'answer' => 'After booking, you will receive a confirmation email with joining details. For phone consultations, our astrologer will call you at the scheduled time.'],
                    ['question' => 'Can I reschedule my consultation?', 'answer' => 'Yes, you can reschedule up to 4 hours before the scheduled time by contacting our support team.'],
                    ['question' => 'What should I prepare before the consultation?', 'answer' => 'Have your birth details (date, time, place) ready. Also prepare a list of specific questions you want to discuss.'],
                    ['question' => 'What is the difference between 30, 45, and 60 minute sessions?', 'answer' => '30 minutes is ideal for 1-2 specific questions. 45 minutes allows deeper analysis. 60 minutes provides comprehensive life guidance covering multiple areas.'],
                ],
                'meta_title' => 'Astrology Consultation | Book Expert Astrologer Session',
                'meta_description' => 'Book a personalized astrology consultation. 30, 45, or 60 minute sessions with expert astrologers. Career, love, and life guidance.',
                'meta_keywords' => 'astrology consultation, astrologer booking, online astrology session, vedic astrology consultation',
                'requires_auth' => true,
                'requires_captcha' => true,
                'delivery_time' => 'Scheduled session',
                'is_active' => true,
                'sort_order' => 6,
            ]
        );

        // Create tiers for consultation durations
        ServiceTier::updateOrCreate(
            ['service_id' => $service->id, 'slug' => '30-min'],
            [
                'name' => '30 Minutes',
                'description' => 'Quick consultation for 1-2 specific questions.',
                'price' => 499.00,
                'currency' => 'INR',
                'features' => ['30 minute session', '1-2 questions covered', 'Basic remedies'],
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        ServiceTier::updateOrCreate(
            ['service_id' => $service->id, 'slug' => '45-min'],
            [
                'name' => '45 Minutes',
                'description' => 'Extended session for deeper analysis and multiple questions.',
                'price' => 749.00,
                'currency' => 'INR',
                'features' => ['45 minute session', 'Multiple questions', 'Detailed remedies', 'Follow-up notes'],
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        ServiceTier::updateOrCreate(
            ['service_id' => $service->id, 'slug' => '60-min'],
            [
                'name' => '60 Minutes',
                'description' => 'Comprehensive session covering all life areas with detailed guidance.',
                'price' => 999.00,
                'currency' => 'INR',
                'features' => ['60 minute session', 'Comprehensive analysis', 'All life areas covered', 'Detailed remedies', 'Priority follow-up support'],
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $this->createFormFields($service, [
            ['field_name' => 'type', 'field_label' => 'Consultation Type', 'field_type' => 'select', 'validation_rules' => 'required|string', 'is_required' => true, 'section' => 'consultation', 'section_label' => 'Consultation Details', 'sort_order' => 1, 'options' => [
                ['value' => 'phone', 'label' => 'Phone Consultation'],
                ['value' => 'video', 'label' => 'Video Call'],
                ['value' => 'chat', 'label' => 'Chat Consultation'],
            ]],
            ['field_name' => 'scheduled_at', 'field_label' => 'Preferred Date & Time', 'field_type' => 'datetime', 'validation_rules' => 'required|date|after:now', 'is_required' => true, 'section' => 'consultation', 'sort_order' => 2, 'help_text' => 'Select your preferred date and time for the consultation'],
            ['field_name' => 'notes', 'field_label' => 'Additional Notes', 'field_type' => 'textarea', 'placeholder' => 'Any specific questions or concerns you want to discuss...', 'validation_rules' => 'nullable|string', 'is_required' => false, 'section' => 'consultation', 'sort_order' => 3],
        ]);
    }

    private function seedPoojaBooking(): void
    {
        $service = Service::updateOrCreate(
            ['slug' => 'pooja-booking'],
            [
                'name' => 'Pooja Booking',
                'type' => 'pooja',
                'short_description' => 'Book temple poojas, home ceremonies, and connect with experienced pandits.',
                'description' => 'Book authentic Vedic poojas performed by experienced pandits. Choose from temple poojas, home ceremonies, Jaap & Homam, and special occasion rituals. All poojas are performed with proper Vedic rituals and mantras.',
                'icon' => 'fas fa-pray',
                'base_price' => 0.00,
                'currency' => 'INR',
                'has_tiers' => false,
                'features' => [
                    'Authentic Vedic rituals performed by experienced pandits',
                    'Temple and home pooja options available',
                    'Personalized sankalp with your name and gotra',
                    'Prasad delivery available for temple poojas',
                    'Video recording of the pooja ceremony',
                    'Post-pooja guidance and recommendations',
                ],
                'faq' => [
                    ['question' => 'How are the poojas performed?', 'answer' => 'All poojas are performed by experienced pandits following authentic Vedic rituals. For temple poojas, they are conducted at renowned temples. For home poojas, a pandit visits your location.'],
                    ['question' => 'Can I watch the pooja live?', 'answer' => 'Yes, for temple poojas we provide a live video link so you can watch the ceremony in real-time. A recording is also shared afterwards.'],
                    ['question' => 'What is Gotra and why is it needed?', 'answer' => 'Gotra identifies your ancestral lineage and is used during the Sankalp (sacred vow) at the beginning of the pooja. If you don\'t know your Gotra, you can use "Kashyap" as a general option.'],
                    ['question' => 'How far in advance should I book?', 'answer' => 'We recommend booking at least 3-5 days in advance to ensure availability. For special occasions and auspicious dates, earlier booking is recommended.'],
                ],
                'meta_title' => 'Pooja Booking | Book Vedic Poojas & Rituals Online',
                'meta_description' => 'Book authentic Vedic poojas online. Temple poojas, home ceremonies, Jaap & Homam by experienced pandits. Live video and prasad delivery available.',
                'meta_keywords' => 'pooja booking, online pooja, vedic rituals, temple pooja, pandit booking, homam',
                'requires_auth' => true,
                'requires_captcha' => true,
                'delivery_time' => 'Scheduled ceremony',
                'is_active' => true,
                'sort_order' => 7,
            ]
        );

        $this->createFormFields($service, [
            ['field_name' => 'devotee_name', 'field_label' => 'Devotee Name', 'field_type' => 'text', 'placeholder' => 'Enter devotee\'s full name', 'validation_rules' => 'required|string|max:255', 'is_required' => true, 'section' => 'devotee', 'section_label' => 'Devotee Details', 'sort_order' => 1],
            ['field_name' => 'phone', 'field_label' => 'Phone Number', 'field_type' => 'tel', 'placeholder' => 'Enter phone number', 'validation_rules' => 'required|string', 'is_required' => true, 'section' => 'devotee', 'sort_order' => 2],
            ['field_name' => 'email', 'field_label' => 'Email', 'field_type' => 'email', 'placeholder' => 'Enter email address', 'validation_rules' => 'nullable|email', 'is_required' => false, 'section' => 'devotee', 'sort_order' => 3],
            ['field_name' => 'gotra', 'field_label' => 'Gotra', 'field_type' => 'text', 'placeholder' => 'Enter your gotra (e.g., Kashyap)', 'validation_rules' => 'nullable|string|max:100', 'is_required' => false, 'section' => 'devotee', 'sort_order' => 4, 'help_text' => 'If unknown, you can use "Kashyap" as a general option'],
            ['field_name' => 'scheduled_at', 'field_label' => 'Preferred Date & Time', 'field_type' => 'datetime', 'validation_rules' => 'required|date|after:now', 'is_required' => true, 'section' => 'booking', 'section_label' => 'Booking Details', 'sort_order' => 5, 'help_text' => 'Select your preferred date and time for the pooja'],
            ['field_name' => 'special_requirements', 'field_label' => 'Special Requirements', 'field_type' => 'textarea', 'placeholder' => 'Any special requirements or instructions for the pooja...', 'validation_rules' => 'nullable|string', 'is_required' => false, 'section' => 'booking', 'sort_order' => 6],
        ]);
    }

    /**
     * Create form fields for a service using updateOrCreate for idempotency.
     *
     * @param Service $service
     * @param array $fields
     */
    private function createFormFields(Service $service, array $fields): void
    {
        foreach ($fields as $fieldData) {
            ServiceFormField::updateOrCreate(
                [
                    'service_id' => $service->id,
                    'field_name' => $fieldData['field_name'],
                ],
                array_merge($fieldData, ['service_id' => $service->id])
            );
        }
    }
}
