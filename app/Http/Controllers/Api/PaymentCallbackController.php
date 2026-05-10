<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentCallbackController extends Controller
{
    /**
     * Handle successful payment return from external provider.
     *
     * PayPal redirects here after user approves payment.
     * The `token` query param is used to identify the order via payment_token.
     * PayPal also sends its order token which is used for capture.
     */
    public function success(Request $request)
    {
        $token = $request->query('token');

        if (empty($token)) {
            return response()->json([
                'message' => 'Missing payment token.',
            ], 400);
        }

        // Look up order by payment_token
        $order = Order::where('payment_token', $token)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Invalid payment token.',
            ], 400);
        }

        // Idempotent: if already paid, just redirect with success
        if ($order->payment_status === 'paid') {
            return redirect($this->getSpaRedirectUrl('success'));
        }

        // Capture the PayPal payment
        try {
            $gateway = PaymentGateway::where('code', 'paypal')->first();

            if (!$gateway) {
                Log::error('Payment callback failed: PayPal gateway not found', [
                    'order_number' => $order->order_number,
                    'timestamp' => now()->toIso8601String(),
                ]);

                $order->update(['payment_status' => 'failed']);

                return redirect($this->getSpaRedirectUrl('error'));
            }

            $provider = $this->getPayPalProvider($gateway);

            // Use the token to capture the PayPal order
            $response = $provider->capturePaymentOrder($token);

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                $transactionId = $response['id'] ?? $token;

                $order->update([
                    'payment_status' => 'paid',
                    'transaction_id' => $transactionId,
                ]);

                return redirect($this->getSpaRedirectUrl('success'));
            }

            // Capture did not return COMPLETED status
            Log::error('Payment capture failed', [
                'order_number' => $order->order_number,
                'provider_response' => $response,
                'timestamp' => now()->toIso8601String(),
            ]);

            $order->update(['payment_status' => 'failed']);

            return redirect($this->getSpaRedirectUrl('error'));
        } catch (\Exception $e) {
            Log::error('Payment capture exception', [
                'order_number' => $order->order_number,
                'provider_response' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ]);

            $order->update(['payment_status' => 'failed']);

            return redirect($this->getSpaRedirectUrl('error'));
        }
    }

    /**
     * Handle payment cancellation from external provider.
     */
    public function cancel(Request $request)
    {
        $token = $request->query('token');

        if (empty($token)) {
            return response()->json([
                'message' => 'Missing payment token.',
            ], 400);
        }

        // Look up order by payment_token
        $order = Order::where('payment_token', $token)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Invalid payment token.',
            ], 400);
        }

        // Idempotent: if already paid, don't cancel - redirect with success
        if ($order->payment_status === 'paid') {
            return redirect($this->getSpaRedirectUrl('success'));
        }

        // Update order as cancelled
        $order->update([
            'payment_status' => 'failed',
            'status' => 'cancelled',
        ]);

        return redirect($this->getSpaRedirectUrl('cancelled'));
    }

    /**
     * Get the SPA redirect URL with the payment status hash fragment.
     */
    private function getSpaRedirectUrl(string $status): string
    {
        $baseUrl = env('FRONTEND_URL', config('app.url'));

        return rtrim($baseUrl, '/') . '#payment=' . $status;
    }

    /**
     * Configure and return a PayPal provider instance.
     */
    private function getPayPalProvider(PaymentGateway $gateway): PayPalClient
    {
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
}
