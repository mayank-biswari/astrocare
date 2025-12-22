<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentGateway;

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
        // Mock payment - randomly succeeds or fails for testing
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
        // Razorpay integration logic here
        return ['success' => false, 'message' => 'Razorpay not configured'];
    }

    private function processStripe(Order $order, PaymentGateway $gateway)
    {
        // Stripe integration logic here
        return ['success' => false, 'message' => 'Stripe not configured'];
    }

    private function processPayPal(Order $order, PaymentGateway $gateway)
    {
        // PayPal integration logic here
        return ['success' => false, 'message' => 'PayPal not configured'];
    }
}
