<?php

namespace Database\Seeders;

use App\Models\CountryCode;
use Illuminate\Database\Seeder;

class CountryCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'India', 'iso_code' => 'IN', 'dial_code' => '+91', 'phone_digits' => 10, 'flag' => '🇮🇳', 'sort_order' => 1],
            ['name' => 'United States', 'iso_code' => 'US', 'dial_code' => '+1', 'phone_digits' => 10, 'flag' => '🇺🇸', 'sort_order' => 2],
            ['name' => 'United Kingdom', 'iso_code' => 'GB', 'dial_code' => '+44', 'phone_digits' => 10, 'flag' => '🇬🇧', 'sort_order' => 3],
            ['name' => 'Canada', 'iso_code' => 'CA', 'dial_code' => '+1', 'phone_digits' => 10, 'flag' => '🇨🇦', 'sort_order' => 4],
            ['name' => 'Australia', 'iso_code' => 'AU', 'dial_code' => '+61', 'phone_digits' => 9, 'flag' => '🇦🇺', 'sort_order' => 5],
            ['name' => 'United Arab Emirates', 'iso_code' => 'AE', 'dial_code' => '+971', 'phone_digits' => 9, 'flag' => '🇦🇪', 'sort_order' => 6],
            ['name' => 'Singapore', 'iso_code' => 'SG', 'dial_code' => '+65', 'phone_digits' => 8, 'flag' => '🇸🇬', 'sort_order' => 7],
            ['name' => 'Malaysia', 'iso_code' => 'MY', 'dial_code' => '+60', 'phone_digits' => 10, 'flag' => '🇲🇾', 'sort_order' => 8],
            ['name' => 'Nepal', 'iso_code' => 'NP', 'dial_code' => '+977', 'phone_digits' => 10, 'flag' => '🇳🇵', 'sort_order' => 9],
            ['name' => 'Sri Lanka', 'iso_code' => 'LK', 'dial_code' => '+94', 'phone_digits' => 9, 'flag' => '🇱🇰', 'sort_order' => 10],
            ['name' => 'Bangladesh', 'iso_code' => 'BD', 'dial_code' => '+880', 'phone_digits' => 10, 'flag' => '🇧🇩', 'sort_order' => 11],
            ['name' => 'Pakistan', 'iso_code' => 'PK', 'dial_code' => '+92', 'phone_digits' => 10, 'flag' => '🇵🇰', 'sort_order' => 12],
            ['name' => 'Germany', 'iso_code' => 'DE', 'dial_code' => '+49', 'phone_digits' => 11, 'flag' => '🇩🇪', 'sort_order' => 13],
            ['name' => 'France', 'iso_code' => 'FR', 'dial_code' => '+33', 'phone_digits' => 9, 'flag' => '🇫🇷', 'sort_order' => 14],
            ['name' => 'Italy', 'iso_code' => 'IT', 'dial_code' => '+39', 'phone_digits' => 10, 'flag' => '🇮🇹', 'sort_order' => 15],
            ['name' => 'Spain', 'iso_code' => 'ES', 'dial_code' => '+34', 'phone_digits' => 9, 'flag' => '🇪🇸', 'sort_order' => 16],
            ['name' => 'Netherlands', 'iso_code' => 'NL', 'dial_code' => '+31', 'phone_digits' => 9, 'flag' => '🇳🇱', 'sort_order' => 17],
            ['name' => 'Switzerland', 'iso_code' => 'CH', 'dial_code' => '+41', 'phone_digits' => 9, 'flag' => '🇨🇭', 'sort_order' => 18],
            ['name' => 'Sweden', 'iso_code' => 'SE', 'dial_code' => '+46', 'phone_digits' => 9, 'flag' => '🇸🇪', 'sort_order' => 19],
            ['name' => 'Norway', 'iso_code' => 'NO', 'dial_code' => '+47', 'phone_digits' => 8, 'flag' => '🇳🇴', 'sort_order' => 20],
            ['name' => 'Denmark', 'iso_code' => 'DK', 'dial_code' => '+45', 'phone_digits' => 8, 'flag' => '🇩🇰', 'sort_order' => 21],
            ['name' => 'Japan', 'iso_code' => 'JP', 'dial_code' => '+81', 'phone_digits' => 10, 'flag' => '🇯🇵', 'sort_order' => 22],
            ['name' => 'China', 'iso_code' => 'CN', 'dial_code' => '+86', 'phone_digits' => 11, 'flag' => '🇨🇳', 'sort_order' => 23],
            ['name' => 'South Korea', 'iso_code' => 'KR', 'dial_code' => '+82', 'phone_digits' => 10, 'flag' => '🇰🇷', 'sort_order' => 24],
            ['name' => 'Thailand', 'iso_code' => 'TH', 'dial_code' => '+66', 'phone_digits' => 9, 'flag' => '🇹🇭', 'sort_order' => 25],
            ['name' => 'Indonesia', 'iso_code' => 'ID', 'dial_code' => '+62', 'phone_digits' => 12, 'flag' => '🇮🇩', 'sort_order' => 26],
            ['name' => 'Philippines', 'iso_code' => 'PH', 'dial_code' => '+63', 'phone_digits' => 10, 'flag' => '🇵🇭', 'sort_order' => 27],
            ['name' => 'Vietnam', 'iso_code' => 'VN', 'dial_code' => '+84', 'phone_digits' => 9, 'flag' => '🇻🇳', 'sort_order' => 28],
            ['name' => 'South Africa', 'iso_code' => 'ZA', 'dial_code' => '+27', 'phone_digits' => 9, 'flag' => '🇿🇦', 'sort_order' => 29],
            ['name' => 'Nigeria', 'iso_code' => 'NG', 'dial_code' => '+234', 'phone_digits' => 10, 'flag' => '🇳🇬', 'sort_order' => 30],
            ['name' => 'Kenya', 'iso_code' => 'KE', 'dial_code' => '+254', 'phone_digits' => 9, 'flag' => '🇰🇪', 'sort_order' => 31],
            ['name' => 'Egypt', 'iso_code' => 'EG', 'dial_code' => '+20', 'phone_digits' => 10, 'flag' => '🇪🇬', 'sort_order' => 32],
            ['name' => 'Saudi Arabia', 'iso_code' => 'SA', 'dial_code' => '+966', 'phone_digits' => 9, 'flag' => '🇸🇦', 'sort_order' => 33],
            ['name' => 'Qatar', 'iso_code' => 'QA', 'dial_code' => '+974', 'phone_digits' => 8, 'flag' => '🇶🇦', 'sort_order' => 34],
            ['name' => 'Kuwait', 'iso_code' => 'KW', 'dial_code' => '+965', 'phone_digits' => 8, 'flag' => '🇰🇼', 'sort_order' => 35],
            ['name' => 'Bahrain', 'iso_code' => 'BH', 'dial_code' => '+973', 'phone_digits' => 8, 'flag' => '🇧🇭', 'sort_order' => 36],
            ['name' => 'Oman', 'iso_code' => 'OM', 'dial_code' => '+968', 'phone_digits' => 8, 'flag' => '🇴🇲', 'sort_order' => 37],
            ['name' => 'New Zealand', 'iso_code' => 'NZ', 'dial_code' => '+64', 'phone_digits' => 9, 'flag' => '🇳🇿', 'sort_order' => 38],
            ['name' => 'Ireland', 'iso_code' => 'IE', 'dial_code' => '+353', 'phone_digits' => 9, 'flag' => '🇮🇪', 'sort_order' => 39],
            ['name' => 'Portugal', 'iso_code' => 'PT', 'dial_code' => '+351', 'phone_digits' => 9, 'flag' => '🇵🇹', 'sort_order' => 40],
            ['name' => 'Brazil', 'iso_code' => 'BR', 'dial_code' => '+55', 'phone_digits' => 11, 'flag' => '🇧🇷', 'sort_order' => 41],
            ['name' => 'Mexico', 'iso_code' => 'MX', 'dial_code' => '+52', 'phone_digits' => 10, 'flag' => '🇲🇽', 'sort_order' => 42],
            ['name' => 'Argentina', 'iso_code' => 'AR', 'dial_code' => '+54', 'phone_digits' => 10, 'flag' => '🇦🇷', 'sort_order' => 43],
            ['name' => 'Russia', 'iso_code' => 'RU', 'dial_code' => '+7', 'phone_digits' => 10, 'flag' => '🇷🇺', 'sort_order' => 44],
            ['name' => 'Turkey', 'iso_code' => 'TR', 'dial_code' => '+90', 'phone_digits' => 10, 'flag' => '🇹🇷', 'sort_order' => 45],
            ['name' => 'Israel', 'iso_code' => 'IL', 'dial_code' => '+972', 'phone_digits' => 9, 'flag' => '🇮🇱', 'sort_order' => 46],
            ['name' => 'Maldives', 'iso_code' => 'MV', 'dial_code' => '+960', 'phone_digits' => 7, 'flag' => '🇲🇻', 'sort_order' => 47],
            ['name' => 'Myanmar', 'iso_code' => 'MM', 'dial_code' => '+95', 'phone_digits' => 9, 'flag' => '🇲🇲', 'sort_order' => 48],
            ['name' => 'Fiji', 'iso_code' => 'FJ', 'dial_code' => '+679', 'phone_digits' => 7, 'flag' => '🇫🇯', 'sort_order' => 49],
            ['name' => 'Mauritius', 'iso_code' => 'MU', 'dial_code' => '+230', 'phone_digits' => 8, 'flag' => '🇲🇺', 'sort_order' => 50],
        ];

        foreach ($countries as $country) {
            CountryCode::updateOrCreate(
                ['iso_code' => $country['iso_code']],
                $country
            );
        }
    }
}
