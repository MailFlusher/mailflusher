<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mime\Email;

class SubscriptionEnded extends Notification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    /**
     * @param  array{aliases:int,recipients:int,rules:int,domains:int}  $deactivated
     */
    public function __construct(public string $previousPlanName, public array $deactivated) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $recipient = $notifiable->defaultRecipient;
        $fingerprint = $recipient->should_encrypt ? $recipient->fingerprint : null;

        return (new MailMessage)
            ->subject("Your {$this->previousPlanName} subscription has ended")
            ->markdown('mail.subscription_ended', [
                'previousPlanName' => $this->previousPlanName,
                'deactivated' => $this->deactivated,
                'userId' => $notifiable->id,
                'recipientId' => $recipient->id,
                'emailType' => 'SE',
                'fingerprint' => $fingerprint,
            ])
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()
                    ->addTextHeader('Feedback-ID', 'SE:mailflusher');
            });
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
