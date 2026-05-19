<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mime\Email;

class InvoicePaymentFailed extends Notification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public function __construct(public ?int $amountCents = null, public ?string $currency = null) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $recipient = $notifiable->defaultRecipient;
        $fingerprint = $recipient->should_encrypt ? $recipient->fingerprint : null;

        $amountDisplay = null;
        if ($this->amountCents && $this->currency) {
            $amountDisplay = strtoupper($this->currency).' '.number_format($this->amountCents / 100, 2);
        }

        return (new MailMessage)
            ->subject('Payment failed — please update your billing details')
            ->markdown('mail.invoice_payment_failed', [
                'amountDisplay' => $amountDisplay,
                'planName' => $notifiable->planConfig()['name'],
                'userId' => $notifiable->id,
                'recipientId' => $recipient->id,
                'emailType' => 'IPF',
                'fingerprint' => $fingerprint,
            ])
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()
                    ->addTextHeader('Feedback-ID', 'IPF:mailflusher');
            });
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
