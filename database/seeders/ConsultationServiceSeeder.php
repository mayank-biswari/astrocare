<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ConsultationServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Chat Consultation',
                'type' => 'consultation',
                'description' => 'Get instant answers to your questions through live chat with our expert astrologers.',
                'price' => 299,
                'is_active' => true
            ],
            [
                'name' => 'Video Consultation', 
                'type' => 'consultation',
                'description' => 'Face-to-face video consultation for detailed astrological guidance and remedies.',
                'price' => 599,
                'is_active' => true
            ],
            [
                'name' => 'Phone Consultation',
                'type' => 'consultation', 
                'description' => 'Personal phone consultation with experienced astrologers for comprehensive guidance.',
                'price' => 399,
                'is_active' => true
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
