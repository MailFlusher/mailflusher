<?php

namespace App\Providers;

use App\Listeners\CheckIfShouldBlock;
use App\Listeners\SendIncorrectOtpNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSending;
use PragmaRX\Google2FALaravel\Events\LoginFailed;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MessageSending::class => [
            CheckIfShouldBlock::class,
        ],
        LoginFailed::class => [
            SendIncorrectOtpNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Override to prevent the framework from auto-registering the email
     * verification listener (which causes duplicate emails).
     * We register it manually in boot() exactly once instead.
     */
    protected function configureEmailVerification(): void
    {
        // Intentionally empty — we handle this in boot()
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
