<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'INR',
                'name' => 'Indian Rupee',
                'symbol' => '₹',
                'exchange_rate' => 1.0000,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 0.0120,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 0.0110,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'exchange_rate' => 0.0095,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 4
            ]
        ];

        foreach ($currencies as $currency) {
            \App\Models\Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
