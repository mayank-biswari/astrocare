<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'Razorpay',
                'code' => 'razorpay',
                'description' => 'Accept payments via Razorpay',
                'supported_currencies' => ['INR'],
                'credentials' => [
                    'key_id' => '',
                    'key_secret' => ''
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'description' => 'Accept payments via Stripe',
                'supported_currencies' => ['USD', 'EUR', 'GBP', 'INR'],
                'credentials' => [
                    'publishable_key' => '',
                    'secret_key' => ''
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'description' => 'Accept payments via PayPal',
                'supported_currencies' => ['USD', 'EUR', 'GBP', 'AUD', 'CAD'],
                'credentials' => [
                    'client_id' => '',
                    'client_secret' => ''
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Fake Payment (Testing)',
                'code' => 'fake',
                'description' => 'Fake payment gateway for testing purposes',
                'supported_currencies' => ['INR', 'USD', 'EUR', 'GBP'],
                'credentials' => null,
                'is_active' => true,
                'is_test_mode' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Cash on Delivery',
                'code' => 'cod',
                'description' => 'Pay when you receive the product',
                'supported_currencies' => ['INR', 'USD', 'EUR', 'GBP'],
                'credentials' => null,
                'is_active' => true,
                'is_test_mode' => false,
                'sort_order' => 5
            ]
        ];

        foreach ($gateways as $gateway) {
            \App\Models\PaymentGateway::updateOrCreate(
                ['code' => $gateway['code']],
                $gateway
            );
        }
    }
}
