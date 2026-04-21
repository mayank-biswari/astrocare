<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentGateway;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    private function getProvider()
    {
        $gateway = PaymentGateway::where('code', 'paypal')->first();
        $credentials = $gateway->credentials;
        $mode = $gateway->is_test_mode ? 'sandbox' : 'live';

        config([
            'paypal.mode' => $mode,
            "paypal.{$mode}.client_id" => $credentials['client_id'],
            "paypal.{$mode}.client_secret" => $credentials['client_secret'],
            'paypal.validate_ssl' => !$gateway->is_test_mode,
        ]);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        return $provider;
    }

    public function success()
    {
        $token = request('token');

        try {
            $provider = $this->getProvider();
            $response = $provider->capturePaymentOrder($token);

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                $orderId = session('paypal_order_id');
                $order = Order::find($orderId);

                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'transaction_id' => $response['id'] ?? $token,
                    ]);

                    session()->forget(['paypal_order_id', 'cart', 'consultation_booking', 'kundli_checkout', 'pooja_booking', 'question_checkout']);

                    return redirect()->route('dashboard.orders')->with('success', 'Payment successful! Order #' . $order->order_number . ' confirmed.');
                }
            }
        } catch (\Exception $e) {
            \Log::error('PayPal Capture Error', ['error' => $e->getMessage()]);
        }

        return redirect()->route('shop.index')->with('error', 'Payment could not be verified. Please contact support.');
    }

    public function cancel()
    {
        $orderId = session('paypal_order_id');

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                ]);
            }
            session()->forget('paypal_order_id');
        }

        return redirect()->route('shop.index')->with('error', 'Payment was cancelled.');
    }
}
