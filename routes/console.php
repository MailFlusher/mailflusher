<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Schedule::command('mailflusher:reset-bandwidth')->monthlyOn(1, '00:00');
Schedule::command('mailflusher:check-domains-sending-verification')->daily();
Schedule::command('mailflusher:check-domains-mx-validation')->daily();
Schedule::command('mailflusher:clear-failed-deliveries')->daily();
Schedule::command('mailflusher:clear-outbound-messages')->everySixHours();
Schedule::command('mailflusher:expire-burner-aliases')->hourly();
Schedule::command('mailflusher:prune-redirect-tokens')->daily();
Schedule::command('mailflusher:prune-ghost-emails')->daily();
Schedule::command('mailflusher:email-users-with-token-expiring-soon')->daily();
Schedule::command('mailflusher:process-plan-lifecycle')->dailyAt('09:00');
Schedule::command('mailflusher:parse-postfix-mail-log')->everyFiveMinutes();
Schedule::command('auth:clear-resets')->daily();
Schedule::command('sanctum:prune-expired --hours=168')->daily();
Schedule::command('cache:prune-stale-tags')->hourly();
