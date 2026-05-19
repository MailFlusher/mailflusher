<?php

namespace App\Services;

use App\Models\PlanGrant;
use App\Models\User;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;

class PlanService
{
    /**
     * Characters allowed in a $source value. Restricting to this set is a
     * defense-in-depth measure against log injection should a future caller
     * ever interpolate user-controlled data into a source string.
     */
    private const SOURCE_PATTERN = '/^[A-Za-z0-9_:.\-\/]{1,255}$/';

    public function __construct(protected PlanDowngradeService $downgradeService) {}

    protected function assertValidSource(string $source): void
    {
        if (! preg_match(self::SOURCE_PATTERN, $source)) {
            throw new \InvalidArgumentException("Invalid plan grant source: {$source}");
        }
    }

    /**
     * Grant a plan to a user. The single entry point for upgrades — signup trial,
     * retroactive trial, promo codes, Stripe subscriptions all funnel through here.
     *
     * $duration null means indefinite (e.g. a Cashier-managed paid subscription).
     * Non-trial grants always clear trial fields, so a trial-to-paid conversion
     * removes the stale trial_ends_at.
     */
    public function grantPlan(
        User $user,
        string $plan,
        ?CarbonInterval $duration,
        string $source,
        bool $isTrial = false,
    ): void {
        if ($isTrial && $duration === null) {
            throw new \InvalidArgumentException('Trial grants must have a finite duration.');
        }

        $this->assertValidSource($source);

        $startedAt = now();
        $endsAt = $duration ? $startedAt->copy()->add($duration) : null;

        DB::transaction(function () use ($user, $plan, $startedAt, $endsAt, $source, $isTrial) {
            $attributes = [
                'plan' => $plan,
                'plan_expires_at' => $endsAt,
            ];

            if ($isTrial) {
                $attributes['trial_ends_at'] = $endsAt;
                $attributes['has_used_trial'] = true;
                $attributes['trial_reminder_stage'] = null;
            } else {
                // Non-trial grants clear trial state (e.g. trial-to-paid conversion).
                $attributes['trial_ends_at'] = null;
                $attributes['trial_reminder_stage'] = null;
            }

            $user->update($attributes);

            PlanGrant::create([
                'user_id' => $user->id,
                'plan' => $plan,
                'started_at' => $startedAt,
                'ends_at' => $endsAt,
                'source' => $source,
            ]);
        });
    }

    /**
     * Move a user back to the free plan. Runs PlanDowngradeService to deactivate
     * over-limit aliases / rules / custom domains, clears trial fields, and writes
     * an audit row. Called by both the lifecycle command (trial/paid expiry) and
     * the Stripe webhook (subscription cancelled).
     *
     * @return array{previous_plan_key:string, previous_plan_name:string, was_trial:bool, deactivated:array{aliases:int,recipients:int,rules:int,domains:int}}
     */
    public function expirePlan(User $user, string $source): array
    {
        $this->assertValidSource($source);

        // Idempotent: if already on free with no pending expiry, do nothing.
        // Means the webhook and the lifecycle cron can both call this without duplicating audit rows.
        if ($user->plan === 'free' && $user->plan_expires_at === null) {
            return [
                'previous_plan_key' => 'free',
                'previous_plan_name' => config('mailflusher.plans.free.name') ?? 'Free',
                'was_trial' => false,
                'deactivated' => ['aliases' => 0, 'recipients' => 0, 'rules' => 0, 'domains' => 0],
            ];
        }

        $previousPlanKey = $user->plan;
        $previousPlanName = config("mailflusher.plans.{$previousPlanKey}.name") ?? $previousPlanKey;
        $wasTrial = $user->trial_ends_at !== null;
        $deactivated = ['aliases' => 0, 'recipients' => 0, 'rules' => 0, 'domains' => 0];

        DB::transaction(function () use ($user, $previousPlanKey, $source, &$deactivated) {
            if ($previousPlanKey !== 'free') {
                $deactivated = $this->downgradeService->downgrade($user, 'free');
            }

            $user->update([
                'plan' => 'free',
                'plan_expires_at' => null,
                'trial_ends_at' => null,
                'trial_reminder_stage' => null,
            ]);

            PlanGrant::create([
                'user_id' => $user->id,
                'plan' => 'free',
                'started_at' => now(),
                'ends_at' => null,
                'source' => $source,
            ]);
        });

        return [
            'previous_plan_key' => $previousPlanKey,
            'previous_plan_name' => $previousPlanName,
            'was_trial' => $wasTrial,
            'deactivated' => $deactivated,
        ];
    }
}
