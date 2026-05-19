<?php

namespace App\Exceptions;

use RuntimeException;

class PromoCodeException extends RuntimeException
{
    public function __construct(string $message, public readonly string $reason)
    {
        parent::__construct($message);
    }

    public static function invalid(): self
    {
        return new self('That code does not look right.', 'invalid');
    }

    public static function notFound(): self
    {
        return new self('That promo code is not valid.', 'not_found');
    }

    public static function inactive(): self
    {
        return new self('That promo code is no longer active.', 'inactive');
    }

    public static function expired(): self
    {
        return new self('That promo code has expired.', 'expired');
    }

    public static function exhausted(): self
    {
        return new self('That promo code has reached its redemption limit.', 'exhausted');
    }

    public static function alreadyRedeemed(): self
    {
        return new self('You have already redeemed this promo code.', 'already_redeemed');
    }

    public static function activeSubscription(): self
    {
        return new self('Cancel your paid subscription before redeeming a promo code.', 'active_subscription');
    }
}
