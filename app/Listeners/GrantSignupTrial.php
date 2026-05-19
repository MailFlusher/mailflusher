<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\PlanService;
use Carbon\CarbonInterval;
use Illuminate\Auth\Events\Verified;

class GrantSignupTrial
{
    public function __construct(protected PlanService $planService) {}

    public function handle(Verified $event): void
    {
        if (! config('mailflusher.trial.enabled')) {
            return;
        }

        $user = $event->user;
        if (! $user instanceof User || $user->has_used_trial) {
            return;
        }

        $this->planService->grantPlan(
            user: $user,
            plan: config('mailflusher.trial.plan'),
            duration: CarbonInterval::days((int) config('mailflusher.trial.duration_days')),
            source: 'signup_trial',
            isTrial: true,
        );
    }
}
