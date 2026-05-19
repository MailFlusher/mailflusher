@component('mail::message')

# Your {{ $previousPlanName }} trial has ended

Your **{{ $previousPlanName }} trial** has ended and your account is now on the **Free plan**.

@if ($deactivated['aliases'] > 0 || ($deactivated['recipients'] ?? 0) > 0 || $deactivated['rules'] > 0 || $deactivated['domains'] > 0)
## What was deactivated

The following items were deactivated to fit Free plan limits. **Nothing was deleted** — anything below can be reactivated by upgrading, or by removing other items so it fits the Free limits.

@if ($deactivated['aliases'] > 0)
- **{{ $deactivated['aliases'] }} {{ $deactivated['aliases'] === 1 ? 'alias' : 'aliases' }}** deactivated (oldest aliases stayed active)
@endif
@if (($deactivated['recipients'] ?? 0) > 0)
- **{{ $deactivated['recipients'] }} {{ $deactivated['recipients'] === 1 ? 'recipient' : 'recipients' }}** deactivated (your default recipient stayed active; forwarding to deactivated recipients is paused)
@endif
@if ($deactivated['rules'] > 0)
- **{{ $deactivated['rules'] }} {{ $deactivated['rules'] === 1 ? 'rule' : 'rules' }}** deactivated (Free plan has no rules)
@endif
@if ($deactivated['domains'] > 0)
- **{{ $deactivated['domains'] }} custom {{ $deactivated['domains'] === 1 ? 'domain' : 'domains' }}** deactivated (Free plan doesn't support custom domains)
@endif
@else
Nothing was deactivated — your usage fit within the Free plan.
@endif

You can upgrade any time and restore everything.

@component('mail::button', ['url' => config('app.url').'/subscription'])
Upgrade
@endcomponent

Thanks for trying {{ config('app.name') }} — even if Free is enough for you today, you're always welcome back.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
