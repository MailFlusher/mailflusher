<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\Account\RetroactiveTrialGranted;
use App\Services\PlanService;
use Carbon\CarbonInterval;
use Illuminate\Console\Command;

class GrantRetroactiveTrials extends Command
{
    protected $signature = 'mailflusher:grant-retroactive-trials
                            {--dry-run : Report eligible users without granting}';

    protected $description = 'Grant a one-time trial to existing free verified users who have not yet used a trial';

    public function handle(PlanService $planService): int
    {
        if (! config('mailflusher.trial.enabled')) {
            $this->warn('Trials are disabled (mailflusher.trial.enabled=false). Aborting.');

            return self::FAILURE;
        }

        $plan = config('mailflusher.trial.plan');
        $durationDays = (int) config('mailflusher.trial.duration_days');

        $users = User::query()
            ->where('plan', 'free')
            ->where('has_used_trial', false)
            ->whereHas('defaultRecipient', fn ($q) => $q->whereNotNull('email_verified_at'))
            ->get();

        $count = $users->count();
        $this->info("Found {$count} eligible users.");

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no changes made.');

            return self::SUCCESS;
        }

        if ($count === 0) {
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($users as $user) {
            $planService->grantPlan(
                user: $user,
                plan: $plan,
                duration: CarbonInterval::days($durationDays),
                source: 'retroactive_trial',
                isTrial: true,
            );

            $user->refresh()->notify(new RetroactiveTrialGranted($durationDays));

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Granted {$count} retroactive {$plan} trials.");

        return self::SUCCESS;
    }
}
