<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\Account\TrialEnded;
use App\Notifications\Account\TrialEndingSoon;
use App\Services\PlanService;
use Illuminate\Console\Command;

class ProcessPlanLifecycle extends Command
{
    protected $signature = 'mailflusher:process-plan-lifecycle';

    protected $description = 'Send trial-expiry reminders and downgrade users whose plan has expired';

    public function handle(PlanService $planService): int
    {
        $now = now();
        $reminderDays = config('mailflusher.trial.reminder_days', [7, 3, 1]);
        sort($reminderDays); // ascending: fire the most relevant (smallest) threshold first

        $remindersFired = $this->processReminders($reminderDays, $now);
        $this->info("Reminders fired: {$remindersFired}");

        $expired = $this->processExpiries($planService, $now);
        $this->info("Plans expired and downgraded: {$expired}");

        return self::SUCCESS;
    }

    protected function processReminders(array $reminderDays, $now): int
    {
        $fired = 0;

        User::query()
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', $now)
            ->chunkById(100, function ($users) use ($reminderDays, $now, &$fired) {
                foreach ($users as $user) {
                    foreach ($reminderDays as $threshold) {
                        $eligibleStage = $user->trial_reminder_stage === null
                            || $user->trial_reminder_stage > $threshold;

                        if ($eligibleStage && $user->trial_ends_at->lte($now->copy()->addDays($threshold))) {
                            $user->notify(new TrialEndingSoon($threshold));
                            $user->update(['trial_reminder_stage' => $threshold]);
                            $fired++;
                            break;
                        }
                    }
                }
            });

        return $fired;
    }

    protected function processExpiries(PlanService $planService, $now): int
    {
        $expired = 0;

        User::query()
            ->whereNotNull('plan_expires_at')
            ->where('plan_expires_at', '<=', $now)
            ->where('plan', '!=', 'free')
            ->chunkById(100, function ($users) use ($planService, &$expired) {
                foreach ($users as $user) {
                    $result = $planService->expirePlan($user, 'trial_or_plan_expired');

                    if ($result['was_trial']) {
                        $user->fresh()->notify(new TrialEnded(
                            $result['previous_plan_name'],
                            $result['deactivated'],
                        ));
                    }

                    $expired++;
                }
            });

        return $expired;
    }
}
