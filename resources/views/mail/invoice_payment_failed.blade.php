@component('mail::message')

# Payment failed

Stripe couldn't process the latest charge on your **{{ $planName }}** subscription@if ($amountDisplay) ({{ $amountDisplay }})@endif. Usually that means your card has expired, was replaced, or doesn't have funds available.

Stripe will keep retrying for a few days. If the retries also fail, your subscription will be cancelled and your account will move back to the Free plan — anything over the Free limit will be deactivated (not deleted).

To avoid that, please update your billing details:

@component('mail::button', ['url' => config('app.url').'/subscription'])
Update billing
@endcomponent

If you've already updated your card, you can ignore this email — the next retry should go through.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
