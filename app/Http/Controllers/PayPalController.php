<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    public function success()
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $token = request('token');
        $response = $provider->capturePaymentOrder($token);

        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            $orderId = session('paypal_order_id');
            $order = Order::find($orderId);

            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'transaction_id' => $response['id'] ?? $token,
                ]);

                session()->forget(['paypal_order_id', 'cart']);

                return redirect()->route('dashboard.orders')->with('success', 'Payment successful! Order #' . $order->order_number . ' confirmed.');
            }
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
