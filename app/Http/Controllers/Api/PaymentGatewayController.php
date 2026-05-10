<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    /**
     * GET /api/payment-gateways?plan_id={plan_id}
     * Returns active gateways supporting the plan's currency.
     */
    public function index(Request $request): JsonResponse
    {
        $planId = $request->query('plan_id');

        $plan = config("plans.{$planId}");

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        $currency = $plan['currency'];

        $gateways = PaymentGateway::getActiveGateways($currency)
            ->map(function ($gateway) {
                return [
                    'code' => $gateway->code,
                    'name' => $gateway->name,
                    'description' => $gateway->description,
                ];
            });

        return response()->json($gateways->values());
    }
}
