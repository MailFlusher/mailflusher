<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mime\Email;

class TrialEndingSoon extends Notification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public function __construct(public int $daysRemaining) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $recipient = $notifiable->defaultRecipient;
        $fingerprint = $recipient->should_encrypt ? $recipient->fingerprint : null;
        $planName = $notifiable->planConfig()['name'];

        $dayWord = $this->daysRemaining === 1 ? 'day' : 'days';

        return (new MailMessage)
            ->subject("Your {$planName} trial ends in {$this->daysRemaining} {$dayWord}")
            ->markdown('mail.trial_ending_soon', [
                'planName' => $planName,
                'daysRemaining' => $this->daysRemaining,
                'trialEndsAt' => $notifiable->trial_ends_at,
                'userId' => $notifiable->id,
                'recipientId' => $recipient->id,
                'emailType' => 'TES',
                'fingerprint' => $fingerprint,
            ])
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()
                    ->addTextHeader('Feedback-ID', 'TES:mailflusher');
            });
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
