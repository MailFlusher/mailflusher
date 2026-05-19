<?php

namespace App\Services;

use App\Exceptions\PromoCodeException;
use App\Models\PromoCode;
use App\Models\PromoRedemption;
use App\Models\User;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;

class PromoCodeService
{
    private const CODE_PATTERN = '/^[A-Z0-9_-]{3,32}$/';

    public function __construct(protected PlanService $planService) {}

    /**
     * Redeem a code for a user. Single transactional path that:
     *  - takes a row lock on the promo_codes row (prevents max_redemptions race)
     *  - refuses if the user has an active Stripe subscription
     *  - refuses if the user has already redeemed this code
     *  - extends an existing plan_expires_at if it is in the future, else starts from now
     *  - grants the plan via PlanService and writes a redemption row
     *
     * @throws PromoCodeException
     */
    public function redeem(User $user, string $code): PromoRedemption
    {
        $normalized = strtoupper(trim($code));

        if (! preg_match(self::CODE_PATTERN, $normalized)) {
            throw PromoCodeException::invalid();
        }

        if ($user->stripe_subscription_id !== null) {
            throw PromoCodeException::activeSubscription();
        }

        return DB::transaction(function () use ($user, $normalized) {
            $promo = PromoCode::where('code', $normalized)->lockForUpdate()->first();

            if ($promo === null) {
                throw PromoCodeException::notFound();
            }

            if (! $promo->active) {
                throw PromoCodeException::inactive();
            }

            if ($promo->isExpired()) {
                throw PromoCodeException::expired();
            }

            if ($promo->isExhausted()) {
                throw PromoCodeException::exhausted();
            }

            $alreadyRedeemed = PromoRedemption::where('user_id', $user->id)
                ->where('promo_code_id', $promo->id)
                ->exists();

            if ($alreadyRedeemed) {
                throw PromoCodeException::alreadyRedeemed();
            }

            // Extension semantics: if user has a plan_expires_at in the future, stack from there
            // so two 30-day codes back-to-back give 60 days, not 30. Otherwise start from now.
            $now = now();
            $remainingSeconds = ($user->plan_expires_at !== null && $user->plan_expires_at->isFuture())
                ? (int) $user->plan_expires_at->diffInSeconds($now, absolute: true)
                : 0;

            $totalDuration = CarbonInterval::seconds($remainingSeconds + $promo->duration_days * 86400);

            $this->planService->grantPlan(
                user: $user,
                plan: $promo->plan,
                duration: $totalDuration,
                source: "promo:{$promo->code}",
            );

            $promo->increment('redemption_count');

            return PromoRedemption::create([
                'user_id' => $user->id,
                'promo_code_id' => $promo->id,
                'plan' => $promo->plan,
                'duration_days' => $promo->duration_days,
                'redeemed_at' => $now,
            ]);
        });
    }
}
