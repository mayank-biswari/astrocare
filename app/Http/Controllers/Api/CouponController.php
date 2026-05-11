<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CouponValidationException;
use App\Http\Controllers\Controller;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Validate a coupon code against a plan.
     *
     * POST /api/coupons/validate
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'coupon_code' => ['required', 'string'],
            'plan_id' => ['required', 'string'],
        ]);

        $planId = $validated['plan_id'];
        $plan = config("plans.{$planId}");

        if (!$plan) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid plan selected',
            ]);
        }

        $originalPrice = (float) $plan['price'];

        $couponService = new CouponService();

        try {
            $coupon = $couponService->validateCoupon($validated['coupon_code']);
        } catch (CouponValidationException $e) {
            return response()->json([
                'valid' => false,
                'message' => $e->getMessage(),
            ]);
        }

        $discount = $couponService->calculateDiscount($coupon, $originalPrice);

        return response()->json([
            'valid' => true,
            'discount_type' => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount_value,
            'discount_amount' => $discount['discount_amount'],
            'discounted_price' => $discount['discounted_price'],
            'original_price' => $originalPrice,
        ]);
    }
}
