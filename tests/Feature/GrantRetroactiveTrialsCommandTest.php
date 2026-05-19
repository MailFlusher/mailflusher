<?php

namespace Tests\Feature;

use App\Notifications\Account\RetroactiveTrialGranted;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GrantRetroactiveTrialsCommandTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[Test]
    public function command_grants_trial_only_to_verified_free_users_without_prior_trial()
    {
        Notification::fake();

        // RecipientFactory defaults email_verified_at to now(), so users are
        // verified unless we explicitly null it out below.
        $eligible = $this->createUser('eligible', 'eligible@test.com');
        $alreadyTrialed = $this->createUser('trialed', 'trialed@test.com', ['has_used_trial' => true]);
        $unverified = $this->createUser('unverified', 'unverified@test.com');
        $unverified->defaultRecipient->update(['email_verified_at' => null]);

        $this->artisan('mailflusher:grant-retroactive-trials')
            ->expectsOutputToContain('Found 1 eligible users.')
            ->assertSuccessful();

        $this->assertSame('pro', $eligible->fresh()->plan);
        $this->assertTrue($eligible->fresh()->onTrial());

        $this->assertSame('free', $alreadyTrialed->fresh()->plan);
        $this->assertSame('free', $unverified->fresh()->plan);

        Notification::assertSentTo($eligible, RetroactiveTrialGranted::class);
        Notification::assertNotSentTo($alreadyTrialed, RetroactiveTrialGranted::class);
        Notification::assertNotSentTo($unverified, RetroactiveTrialGranted::class);
    }

    #[Test]
    public function dry_run_does_not_modify_users()
    {
        Notification::fake();

        // verified by default via RecipientFactory
        $user = $this->createUser('drytest', 'dry@test.com');

        $this->artisan('mailflusher:grant-retroactive-trials', ['--dry-run' => true])
            ->expectsOutputToContain('Dry run')
            ->assertSuccessful();

        $this->assertSame('free', $user->fresh()->plan);
        $this->assertFalse($user->fresh()->has_used_trial);
        Notification::assertNothingSent();
    }

    #[Test]
    public function command_aborts_when_trials_are_disabled()
    {
        config(['mailflusher.trial.enabled' => false]);

        $this->artisan('mailflusher:grant-retroactive-trials')
            ->expectsOutputToContain('disabled')
            ->assertFailed();
    }
}
