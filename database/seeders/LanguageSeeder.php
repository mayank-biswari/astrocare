<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1
            ],
            [
                'code' => 'hi',
                'name' => 'Hindi',
                'native_name' => 'हिंदी',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2
            ]
        ];

        foreach ($languages as $language) {
            \App\Models\Language::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }
    }
}
