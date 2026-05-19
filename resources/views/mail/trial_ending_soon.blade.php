@component('mail::message')

# Your {{ $planName }} trial ends in {{ $daysRemaining }} {{ $daysRemaining === 1 ? 'day' : 'days' }}

Heads up — your **{{ $planName }} trial** ends on **{{ $trialEndsAt->format('jS F Y') }}**.

@if ($daysRemaining === 1)
That's tomorrow. After it ends, your account drops to the Free plan: aliases over the Free limit are deactivated, rules over the Free limit are deactivated, and custom domains stop forwarding.
@else
After it ends, your account drops to the Free plan: aliases over the Free limit are deactivated, rules over the Free limit are deactivated, and custom domains stop forwarding.
@endif

If you'd like to keep your {{ $planName }} features (and your aliases active), upgrade before the trial ends.

@component('mail::button', ['url' => config('app.url').'/subscription'])
Upgrade now
@endcomponent

If you do nothing, nothing breaks — your account simply moves to Free and your deactivated items can be brought back by upgrading or removing other items to fit the Free limits.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
