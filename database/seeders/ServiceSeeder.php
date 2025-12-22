<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Chat Consultation',
                'type' => 'consultation',
                'description' => 'Get instant answers through our chat platform with experienced astrologers.',
                'price' => 299.00,
                'currency' => 'INR',
                'features' => ['Instant messaging', 'Text-based guidance', 'Quick responses'],
                'is_active' => true
            ],
            [
                'name' => 'Video Call Consultation',
                'type' => 'consultation',
                'description' => 'Face-to-face consultation with detailed analysis and guidance.',
                'price' => 599.00,
                'currency' => 'INR',
                'features' => ['Video calling', 'Face-to-face interaction', 'Detailed analysis'],
                'is_active' => true
            ],
            [
                'name' => 'Phone Consultation',
                'type' => 'consultation',
                'description' => 'Personal phone consultation for in-depth astrological guidance.',
                'price' => 499.00,
                'currency' => 'INR',
                'features' => ['Voice calling', 'Personal guidance', 'In-depth analysis'],
                'is_active' => true
            ],
            [
                'name' => 'Basic Kundli',
                'type' => 'kundli',
                'description' => 'Birth chart with planetary positions and basic predictions.',
                'price' => 299.00,
                'currency' => 'INR',
                'features' => ['Birth chart', 'Planetary positions', 'Basic predictions'],
                'is_active' => true
            ],
            [
                'name' => 'Detailed Kundli',
                'type' => 'kundli',
                'description' => 'Comprehensive analysis with Dasha periods and remedies.',
                'price' => 599.00,
                'currency' => 'INR',
                'features' => ['Detailed analysis', 'Dasha periods', 'Remedies', 'Career guidance'],
                'is_active' => true
            ],
            [
                'name' => 'Premium Kundli',
                'type' => 'kundli',
                'description' => 'Complete life analysis with yearly predictions and guidance.',
                'price' => 999.00,
                'currency' => 'INR',
                'features' => ['Complete analysis', 'Yearly predictions', 'Marriage compatibility', 'Health analysis'],
                'is_active' => true
            ],
            [
                'name' => 'Horoscope Matching',
                'type' => 'horoscope_matching',
                'description' => 'Check compatibility between partners with detailed match analysis.',
                'price' => 399.00,
                'currency' => 'INR',
                'features' => ['Compatibility check', 'Match score', 'Detailed report'],
                'is_active' => true
            ],
            [
                'name' => 'Ask a Question',
                'type' => 'question',
                'description' => 'Get specific answers to your questions from expert astrologers.',
                'price' => 199.00,
                'currency' => 'INR',
                'features' => ['Specific answers', 'Expert guidance', 'Quick response'],
                'is_active' => true
            ]
        ];

        foreach ($services as $service) {
            \App\Models\Service::create($service);
        }
    }
}
