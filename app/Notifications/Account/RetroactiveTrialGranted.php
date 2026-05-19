<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mime\Email;

class RetroactiveTrialGranted extends Notification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public function __construct(public int $durationDays) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $freeLimits = config('mailflusher.plans.free');
        $proName = config('mailflusher.plans.pro.name');

        $activeAliases = $notifiable->aliases()->where('active', true)->count();
        $activeRecipients = $notifiable->recipients()->count();
        $activeRules = $notifiable->rules()->where('active', true)->count();
        $activeDomains = $notifiable->domains()->where('active', true)->count();

        $recipient = $notifiable->defaultRecipient;
        $fingerprint = $recipient->should_encrypt ? $recipient->fingerprint : null;

        return (new MailMessage)
            ->subject("You have {$this->durationDays} days of {$proName} — free")
            ->markdown('mail.retroactive_trial_granted', [
                'durationDays' => $this->durationDays,
                'proName' => $proName,
                'trialEndsAt' => $notifiable->trial_ends_at,
                'activeAliases' => $activeAliases,
                'activeRecipients' => $activeRecipients,
                'activeRules' => $activeRules,
                'activeDomains' => $activeDomains,
                'freeAliasLimit' => $freeLimits['aliases'],
                'freeRecipientLimit' => $freeLimits['recipients'],
                'freeRuleLimit' => $freeLimits['rules'],
                'freeCanUseCustomDomains' => (bool) $freeLimits['can_use_custom_domains'],
                'userId' => $notifiable->id,
                'recipientId' => $recipient->id,
                'emailType' => 'RTG',
                'fingerprint' => $fingerprint,
            ])
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()
                    ->addTextHeader('Feedback-ID', 'RTG:mailflusher');
            });
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
