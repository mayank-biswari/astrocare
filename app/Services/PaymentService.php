<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentGateway;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentService
{
    public function processPayment(Order $order, $gatewayCode)
    {
        $gateway = PaymentGateway::where('code', $gatewayCode)->first();

        if (!$gateway) {
            return ['success' => false, 'message' => 'Invalid payment gateway'];
        }

        switch ($gatewayCode) {
            case 'cod':
                return $this->processCOD($order);
            case 'fake':
                return $this->processFakePayment($order);
            case 'razorpay':
                return $this->processRazorpay($order, $gateway);
            case 'stripe':
                return $this->processStripe($order, $gateway);
            case 'paypal':
                return $this->processPayPal($order, $gateway);
            default:
                return ['success' => false, 'message' => 'Payment gateway not implemented'];
        }
    }

    private function processCOD(Order $order)
    {
        return [
            'success' => true,
            'message' => 'Order placed successfully! Pay on delivery.',
            'payment_status' => 'pending'
        ];
    }

    private function processFakePayment(Order $order)
    {
        $success = rand(0, 1) === 1;

        if ($success) {
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => 'FAKE-' . time() . '-' . rand(1000, 9999)
            ]);

            return [
                'success' => true,
                'message' => 'Payment successful! Order #' . $order->order_number . ' confirmed.',
                'payment_status' => 'paid'
            ];
        } else {
            $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled'
            ]);

            return [
                'success' => false,
                'message' => 'Payment failed! Please try again or use a different payment method.',
                'payment_status' => 'failed'
            ];
        }
    }

    private function processRazorpay(Order $order, PaymentGateway $gateway)
    {
        return ['success' => false, 'message' => 'Razorpay not configured'];
    }

    private function processStripe(Order $order, PaymentGateway $gateway)
    {
        return ['success' => false, 'message' => 'Stripe not configured'];
    }

    private function processPayPal(Order $order, PaymentGateway $gateway)
    {
        $credentials = $gateway->credentials;
        $mode = $gateway->is_test_mode ? 'sandbox' : 'live';

        config([
            'paypal.mode' => $mode,
            "paypal.{$mode}.client_id" => $credentials['client_id'],
            "paypal.{$mode}.client_secret" => $credentials['client_secret'],
            'paypal.validate_ssl' => !$gateway->is_test_mode,
        ]);

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $amount = number_format((float) $order->total_amount, 2, '.', '');

            $response = $provider->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $order->order_number,
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => $amount,
                        ],
                    ],
                ],
                'application_context' => [
                    'return_url' => route('paypal.success'),
                    'cancel_url' => route('paypal.cancel'),
                    'brand_name' => config('app.name', 'AstroServices'),
                ],
            ]);

            \Log::info('PayPal Response', ['status' => $response['status'] ?? 'no status']);

            if (isset($response['id']) && isset($response['links'])) {
                session(['paypal_order_id' => $order->id]);

                $approveUrl = collect($response['links'])->firstWhere('rel', 'approve')['href'] ?? null;

                if ($approveUrl) {
                    return [
                        'success' => true,
                        'redirect' => $approveUrl,
                        'payment_status' => 'pending'
                    ];
                }
            }

            $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);

            $errorMsg = is_array($response['message'] ?? null) ? json_encode($response['message']) : ($response['message'] ?? 'Unknown error');
            if (isset($response['error'])) {
                $errorMsg = is_array($response['error']) ? ($response['error']['message'] ?? json_encode($response['error'])) : $response['error'];
            }
            return [
                'success' => false,
                'message' => 'PayPal error: ' . $errorMsg,
            ];
        } catch (\Exception $e) {
            \Log::error('PayPal Exception', ['error' => $e->getMessage()]);
            $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);

            return [
                'success' => false,
                'message' => 'PayPal error: ' . $e->getMessage(),
            ];
        }
    }
}
