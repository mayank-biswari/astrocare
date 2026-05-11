<?php

namespace App\Services;

use App\Exceptions\CouponValidationException;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponService
{
    /**
     * Validate a coupon code.
     *
     * Checks existence, active status, date validity, and usage limit.
     * Returns the Coupon model if valid, or throws CouponValidationException.
     *
     * @throws CouponValidationException
     */
    public function validateCoupon(string $code): Coupon
    {
        $coupon = Coupon::where('code', $code)->first();

        // Check existence and active status
        if (!$coupon || !$coupon->is_active) {
            throw new CouponValidationException('Invalid coupon code');
        }

        // Check date validity
        $today = Carbon::today();
        if ($today->lt($coupon->start_date) || $today->gt($coupon->end_date)) {
            throw new CouponValidationException('This coupon is not currently valid');
        }

        // Check usage limit (0 means unlimited)
        if ($coupon->usage_limit > 0 && $coupon->usage_count >= $coupon->usage_limit) {
            throw new CouponValidationException('This coupon has reached its usage limit');
        }

        return $coupon;
    }

    /**
     * Calculate the discount for a given coupon and original price.
     *
     * @return array{discount_amount: float, discounted_price: float}
     */
    public function calculateDiscount(Coupon $coupon, float $originalPrice): array
    {
        if ($coupon->discount_type === 'fixed') {
            $discountAmount = (float) $coupon->discount_value;
        } else {
            // percentage
            $discountAmount = $originalPrice * (float) $coupon->discount_value / 100;
        }

        $discountAmount = round($discountAmount, 2);
        $discountedPrice = round(max(0, $originalPrice - $discountAmount), 2);

        return [
            'discount_amount' => $discountAmount,
            'discounted_price' => $discountedPrice,
        ];
    }

    /**
     * Increment the usage count of a coupon atomically.
     * Called after successful payment.
     */
    public function incrementUsage(Coupon $coupon): void
    {
        $coupon->increment('usage_count');
    }
}
