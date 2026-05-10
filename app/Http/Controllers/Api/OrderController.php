<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateOrderRequest;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Return the authenticated user's orders (max 50, most recent first).
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($order) {
                $planName = is_array($order->items) && isset($order->items['plan_name'])
                    ? $order->items['plan_name']
                    : null;

                return [
                    'order_number' => $order->order_number,
                    'plan_name' => $planName,
                    'total_amount' => number_format($order->total_amount, 2, '.', ''),
                    'currency' => $order->currency,
                    'created_at' => $order->created_at->toIso8601String(),
                ];
            });

        return response()->json(['orders' => $orders]);
    }

    /**
     * Create a new pending order for a pricing plan and process payment.
     */
    public function store(CreateOrderRequest $request)
    {
        $validated = $request->validated();
        $planId = $validated['plan_id'];
        $paymentGateway = $validated['payment_gateway'];
        $plan = config("plans.{$planId}");

        // Create the order with pending payment status
        $order = Order::create([
            'user_id' => $request->user()->id,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'total_amount' => $plan['price'],
            'currency' => $plan['currency'],
            'status' => 'pending',
            'payment_status' => 'pending',
            'items' => [
                'plan_id' => $planId,
                'plan_name' => $plan['name'],
                'description' => $plan['description'],
            ],
            'shipping_address' => [],
        ]);

        // Generate payment token for callback identification
        $order->payment_token = Str::random(64);
        $order->save();

        // Build callback URLs using named routes
        $returnUrl = route('payment.callback.success', ['token' => $order->payment_token]);
        $cancelUrl = route('payment.callback.cancel', ['token' => $order->payment_token]);

        // Base order response structure
        $orderResponse = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'plan_id' => $planId,
            'plan_name' => $plan['name'],
            'total_amount' => number_format($plan['price'], 2, '.', ''),
            'currency' => $order->currency,
            'status' => $order->status,
        ];

        // Process payment through PaymentService
        try {
            $result = (new PaymentService())->processPayment($order, $paymentGateway, $returnUrl, $cancelUrl);

            // Determine payment outcome
            if ($this->isRedirectFlow($result)) {
                // Redirect flow: order stays pending, return redirect_url
                return response()->json([
                    'order' => $orderResponse,
                    'payment_status' => 'pending',
                    'redirect_url' => $result['redirect'],
                ], 201);
            }

            if ($this->isPaymentSuccess($result)) {
                // Immediate success: set paid, persist transaction_id
                $transactionId = $result['transaction_id'] ?? $order->transaction_id;
                $order->update([
                    'payment_status' => 'paid',
                    'transaction_id' => $transactionId,
                ]);

                return response()->json([
                    'order' => $orderResponse,
                    'payment_status' => 'paid',
                    'transaction_id' => $transactionId,
                ], 201);
            }

            if ($this->isPaymentFailure($result)) {
                // Immediate failure
                $errorMessage = $result['message'] ?? 'Payment failed';
                $order->update(['payment_status' => 'failed']);

                return response()->json([
                    'order' => $orderResponse,
                    'payment_status' => 'failed',
                    'payment_error' => $errorMessage,
                ], 201);
            }

            // Default: success with pending status (e.g., COD)
            // Order already has payment_status 'pending', no update needed
            return response()->json([
                'order' => $orderResponse,
                'payment_status' => 'pending',
            ], 201);
        } catch (\Exception $e) {
            // Exception during payment processing
            Log::error('Payment processing failed', [
                'order_number' => $order->order_number,
                'gateway_code' => $paymentGateway,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $order->update(['payment_status' => 'failed']);

            return response()->json([
                'order' => $orderResponse,
                'payment_status' => 'failed',
                'payment_error' => 'Payment processing failed. Please try again.',
            ], 201);
        }
    }

    /**
     * Determine if the payment result indicates immediate success.
     */
    private function isPaymentSuccess(array $result): bool
    {
        // Explicit paid status from the service takes precedence
        if (isset($result['payment_status']) && $result['payment_status'] === 'paid') {
            return true;
        }

        // If payment_status is explicitly set to something other than 'paid', respect it
        if (isset($result['payment_status']) && $result['payment_status'] !== 'paid') {
            return false;
        }

        // Success without redirect and without explicit payment_status means immediate success
        if (isset($result['success']) && $result['success'] === true && !isset($result['redirect'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the payment result indicates a redirect flow.
     */
    private function isRedirectFlow(array $result): bool
    {
        return isset($result['redirect']) && !empty($result['redirect']);
    }

    /**
     * Determine if the payment result indicates immediate failure.
     */
    private function isPaymentFailure(array $result): bool
    {
        // Explicit failed status
        if (isset($result['payment_status']) && $result['payment_status'] === 'failed') {
            return true;
        }

        // Success flag is explicitly false
        if (isset($result['success']) && $result['success'] === false) {
            return true;
        }

        return false;
    }
}
