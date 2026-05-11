<?php

namespace App\Exceptions;

use Exception;

class CouponValidationException extends Exception
{
    /**
     * Create a new CouponValidationException instance.
     *
     * @param string $message The user-facing error message
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * Create an exception for an invalid or inactive coupon code.
     */
    public static function invalidCode(): self
    {
        return new self('Invalid coupon code');
    }

    /**
     * Create an exception for an expired or not-yet-active coupon.
     */
    public static function notCurrentlyValid(): self
    {
        return new self('This coupon is not currently valid');
    }

    /**
     * Create an exception for a coupon that has reached its usage limit.
     */
    public static function usageLimitReached(): self
    {
        return new self('This coupon has reached its usage limit');
    }
}
