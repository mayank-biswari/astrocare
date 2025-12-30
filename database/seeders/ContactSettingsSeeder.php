<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactSetting;

class ContactSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'admin_email' => 'admin@astroservices.com',
            'contact_phone' => '+91 98765 43210',
            'contact_address' => "123 Astrology Street\nNew Delhi, India 110001",
            'business_hours' => "Monday - Sunday\n9:00 AM - 9:00 PM"
        ];

        foreach ($settings as $key => $value) {
            ContactSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}